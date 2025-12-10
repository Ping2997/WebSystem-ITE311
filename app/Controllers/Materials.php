<?php

namespace App\Controllers;

use App\Models\MaterialsModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Materials extends Controller
{
    protected $materialsModel;

    public function __construct()
    {
        $this->materialsModel = new MaterialsModel();
        helper(['form', 'url']);
    }

    // Upload form + file upload (ADMIN only)
    public function upload($course_id)
    {
        // must be admin or teacher
        if (!session()->get('isLoggedIn') || !in_array(session('role'), ['admin','teacher'], true)) {
            return redirect()->to(base_url('login'));
        }

        // Verify course exists to avoid FK insert failure
        $courseId = (int) $course_id;
        $exists = db_connect()->table('courses')->where('id', $courseId)->countAllResults();
        if ($exists === 0) {
            return redirect()->back()->with('error', 'Invalid course: ID ' . $courseId . ' does not exist.');
        }
        // Handle form submission (robust detection across environments)
        $hasFile = ($this->request->getFile('material_file') !== null && $this->request->getFile('material_file')->getName() !== '')
                || ($this->request->getFile('material') !== null && $this->request->getFile('material')->getName() !== '')
                || (!empty($_FILES));
        $isPost = ($this->request->getMethod() === 'post') || (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST');
        if ($hasFile || $isPost) {
            // Delegate to the private handler that applies the provided upload logic
            return $this->handleFileUpload($courseId);
        }

        return view('Admin/upload_material', ['course_id' => $course_id]);
    }

    // Delete material (ADMIN only)
    public function delete($id)
    {
        if (!session()->get('isLoggedIn') || session('role') !== 'admin') {
            return redirect()->to(base_url('login'));
        }
        $material = $this->materialsModel->find($id);
        if ($material) {
            $path = $this->resolvePath($material['file_path']);
            if (is_file($path)) {
                @unlink($path);
            }
            $this->materialsModel->delete($id);
            return redirect()->back()->with('success', 'Material deleted successfully.');
        }
        return redirect()->back()->with('error', 'Material not found.');
    }

    // Download material (Admin or enrolled students only)
    public function download($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $material = $this->materialsModel->find($id);
        if (!$material) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Admin/Teacher bypass
        if (in_array(session('role'), ['admin','teacher'], true)) {
            $path = $this->resolvePath($material['file_path']);



            
            if (is_file($path)) {
                return $this->response->download($path, null);
            }
            // Secondary attempt: normalize under WRITEPATH explicitly
            $alt = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim((string)$material['file_path'], '\\/');
            $alt = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $alt);
            if (is_file($alt)) {
                return $this->response->download($alt, null);
            }
            return redirect()->back()->with('error', 'File not found on disk.');
        }

        // Students/teachers must be enrolled in the course
        $enrollModel = new EnrollmentModel();
        $userId = (int) (session('userID') ?? 0);
        $courseId = (int) ($material['course_id'] ?? 0);
        $enrolled = $enrollModel->isAlreadyEnrolled($userId, $courseId);
        if (!$enrolled) {
            return redirect()->back()->with('error', 'Access denied: not enrolled in this course.');
        }

        $path = $this->resolvePath($material['file_path']);
        if (is_file($path)) {
            return $this->response->download($path, null);
        }
        $alt = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim((string)$material['file_path'], '\\/');
        $alt = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $alt);
        if (is_file($alt)) {
            return $this->response->download($alt, null);
        }
        return redirect()->back()->with('error', 'File not found on disk.')->with('debug', 'tried=' . $path . ' | alt=' . $alt);
    }

    // List materials for a course (students must be enrolled; admin allowed)
    public function list($course_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $courseId = (int) $course_id;

        // quick guard: ensure course exists
        $exists = db_connect()->table('courses')->where('id', $courseId)->countAllResults();
        if ($exists === 0) {
            return redirect()->back()->with('error', 'Invalid course: ID ' . $courseId . ' does not exist.');
        }

        if (!in_array(session('role'), ['admin','teacher'], true)) {
            $userId = (int) (session('userID') ?? 0);
            $enrollModel = new EnrollmentModel();
            if (!$enrollModel->isAlreadyEnrolled($userId, $courseId)) {
                return redirect()->back()->with('error', 'Access denied: not enrolled in this course.');
            }
        }

        $materials = $this->materialsModel->getMaterialsByCourse($courseId);
        return view('Student/materials_list', ['materials' => $materials]);
    }

    /**
     * Resolve stored path to an absolute filesystem path.
     * Accepts absolute stored path or relative like 'uploads/materials/..'.
     */
    private function resolvePath(string $stored): string
    {
        if ($stored === '') return '';
        // If already absolute
        if (preg_match('/^[A-Za-z]:\\\\|\//', $stored) || strpos($stored, DIRECTORY_SEPARATOR) === 0) {
            return $stored;
        }
        // Try under WRITEPATH first
        $w = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($stored, '\/');
        if (is_file($w)) return $w;
        // Fallback to FCPATH
        $f = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($stored, '\/');
        return $f;
    }

    /**
     * Applies the provided file upload logic for course materials.
     */
    private function handleFileUpload($course_id)
    {
        $validation = \Config\Services::validation();

        // Support both possible input names from views: 'material_file' (current) and 'material' (legacy)
        $inputName = $this->request->getFile('material_file') ? 'material_file' : 'material';
        $validation->setRules([
            $inputName => [
                'label' => 'Material File',
                'rules' => 'uploaded['.$inputName.']|max_size['.$inputName.',10240]|ext_in['.$inputName.',pdf,ppt,pptx]'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()
                ->with('error', 'File validation failed: ' . implode(', ', $validation->getErrors()));
        }

        $file = $this->request->getFile($inputName);

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Create upload directory if it doesn't exist
            $uploadPath = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'materials' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $newName = $file->getRandomName();

            if ($file->move($uploadPath, $newName)) {
                // Save to database
                $data = [
                    'course_id'  => (int) $course_id,
                    'file_name'  => $file->getClientName(),
                    'file_path'  => 'uploads/materials/' . $newName,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                if ($this->materialsModel->insertMaterial($data)) {
                    // Notify all enrolled students that new material was uploaded
                    try {
                        $enrollModel = new EnrollmentModel();
                        $notifModel  = new NotificationModel();

                        // Get course title for friendlier message
                        $courseRow = db_connect()->table('courses')
                            ->select('title')
                            ->where('id', (int) $course_id)
                            ->get()
                            ->getRowArray();
                        $courseTitle = $courseRow['title'] ?? 'a course';

                        $enrollments = $enrollModel->where('course_id', (int) $course_id)->findAll();
                        foreach ($enrollments as $en) {
                            $notifModel->insert([
                                'user_id'    => (int) $en['user_id'],
                                'message'    => 'New material has been uploaded for ' . $courseTitle,
                                'is_read'    => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    } catch (\Throwable $e) {
                        // Do not block upload on notification failure; optionally log
                        log_message('error', 'Material upload notification failed: ' . $e->getMessage());
                    }

                    return redirect()->to(base_url('course/' . (int) $course_id . '/materials'))
                        ->with('success', 'Material uploaded successfully!');
                } else {
                    $errs = $this->materialsModel->errors();
                    $dbErr = $this->materialsModel->db->error();
                    $dbMsg = isset($dbErr['message']) && $dbErr['code'] ? (' DB: ' . $dbErr['message']) : '';
                    return redirect()->back()
                        ->with('error', 'Failed to save material information.' . (!empty($errs) ? ' ' . implode('; ', $errs) : '') . $dbMsg);
                }
            } else {
                $errMsg = method_exists($file, 'getErrorString') ? $file->getErrorString() : 'unknown error';
                return redirect()->back()
                    ->with('error', 'Failed to upload file: ' . $errMsg);
            }
        } else {
            return redirect()->back()
                ->with('error', 'Invalid file or file already moved.');
        }
        // Fallback, should not reach here
        return redirect()->back();
    }
}
