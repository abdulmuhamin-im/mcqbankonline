<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;
use Throwable;

class Users extends BaseController
{
    private function canManage(): bool
    {
        return (bool) session()->get('isLoggedIn')
            && in_array((string) session()->get('role'), ['0', '1'], true);
    }

    private function guard()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->canManage()) {
            return redirect()->to(site_url('dashboard/member'))->with('error', 'Only administrators can manage accounts.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->guard()) return $redirect;
        $db = db_connect();
        $errors = [];
        $users = [];
        $faculty = [];
        $departments = $designations = [];
        try {
            $users = $db->table('mcq_tbl_user')
                ->select('UserID, UserName, FullName, RollID, is_active')
                ->orderBy('UserName', 'ASC')->get()->getResultArray();
        } catch (DatabaseException $e) {
            log_message('error', 'Users directory query failed: {message}', ['message' => $e->getMessage()]);
            $errors[] = 'Could not load accounts. Make sure the FullName database migration has been applied.';
        }
        try {
            $faculty = $db->table('mcq_tbl_author a')
                ->select('a.author_id, a.author_name, a.department_id, d.department_name, a.designation_id, g.designation_name, a.subject_id, s.subject_name')
                ->join('mcq_tbl_departments d', 'd.department_id = a.department_id', 'left')
                ->join('mcq_tbl_designations g', 'g.designation_id = a.designation_id', 'left')
                ->join('mcq_tbl_subject s', 's.subject_id = a.subject_id', 'left')
                ->orderBy('d.department_name', 'ASC')
                ->orderBy("CASE WHEN g.designation_name = 'Professor' THEN 1 WHEN g.designation_name = 'Associate Professor' THEN 2 WHEN g.designation_name = 'Assistant Professor' THEN 3 ELSE 4 END", '', false)
                ->orderBy('g.designation_name', 'ASC')
                ->orderBy('a.author_name', 'ASC')
                ->get()->getResultArray();
            $departments = $db->table('mcq_tbl_departments')->select('department_id, department_name')->orderBy('department_name')->get()->getResultArray();
            $designations = $db->table('mcq_tbl_designations')->select('designation_id, designation_name')->orderBy('designation_name')->get()->getResultArray();
        } catch (DatabaseException $e) {
            log_message('error', 'Faculty directory query failed: {message}', ['message' => $e->getMessage()]);
            $errors[] = 'Could not load faculty records. Check department/designation/subject table names.';
        }
        $roleLabels = ['0' => 'Super Administrator', '1' => 'Administrator', '2' => 'Teaching Faculty', '3' => 'Teaching Faculty / Reviewer'];
        return view('admin/users', [
            'pageTitle' => 'Users & Faculty — MCQ Bank', 'users' => $users, 'faculty' => $faculty,
            'errors' => $errors, 'roleLabels' => $roleLabels,
            'success' => session()->getFlashdata('success'),
        ]);
    }

    public function new()
    {
        if ($redirect = $this->guard()) return $redirect;
        return $this->form(null, []);
    }

    public function edit(int $id)
    {
        if ($redirect = $this->guard()) return $redirect;
        $db = db_connect();
        $user = $db->table('mcq_tbl_user')->where('UserID', $id)->get()->getRowArray();
        if (! $user) return redirect()->to(site_url('master/admin/users'))->with('error', 'User account not found.');
        $author = $db->table('mcq_tbl_author')->where('author_id', $id)->get()->getRowArray();
        $user['author'] = $author ?: [];
        return $this->form($user, []);
    }

    private function form(?array $user, array $errors)
    {
        $db = db_connect();
        $subjects = $db->table('mcq_tbl_subject')->select('subject_id, subject_name, department_id')->orderBy('subject_name')->get()->getResultArray();
        $departments = $db->table('mcq_tbl_departments')->select('department_id, department_name')->orderBy('department_name')->get()->getResultArray();
        $designations = $db->table('mcq_tbl_designations')->select('designation_id, designation_name')->orderBy('designation_name')->get()->getResultArray();
        return view('admin/user_form', [
            'pageTitle' => $user ? 'Edit User Account' : 'Add User Account',
            'user' => $user, 'errors' => $errors, 'subjects' => $subjects,
            'departments' => $departments, 'designations' => $designations,
        ]);
    }

    public function save()
    {
        if ($redirect = $this->guard()) return $redirect;
        $db = db_connect();
        $idRaw = trim((string) $this->request->getPost('UserID'));
        $id = $idRaw === '' ? null : filter_var($idRaw, FILTER_VALIDATE_INT);
        $fullName = trim((string) $this->request->getPost('FullName'));
        $username = trim((string) $this->request->getPost('UserName'));
        $password = (string) $this->request->getPost('Password');
        $role = trim((string) $this->request->getPost('RollID'));
        $isFaculty = in_array($role, ['2', '3'], true);
        $departmentId = filter_var($this->request->getPost('department_id'), FILTER_VALIDATE_INT);
        $designationRaw = trim((string) $this->request->getPost('designation_id'));
        $subjectId = filter_var($this->request->getPost('subject_id'), FILTER_VALIDATE_INT);
        $authorName = trim((string) $this->request->getPost('author_name'));

        $errors = [];
        if ($idRaw !== '' && ($id === false || $id < 1)) $errors[] = 'Invalid user ID.';
        if ($fullName === '' || mb_strlen($fullName) > 255) $errors[] = 'Full name is required (maximum 255 characters).';
        if ($username === '' || mb_strlen($username) > 255) $errors[] = 'Username is required (maximum 255 characters).';
        if (! in_array($role, ['0', '1', '2', '3'], true)) $errors[] = 'Select a valid account role.';
        if ($id === null && $password === '') $errors[] = 'Password is required for a new account.';
        if ($password !== '' && mb_strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
        $duplicate = $db->table('mcq_tbl_user')->where('UserName', $username);
        if ($id) $duplicate->where('UserID !=', $id);
        if ($duplicate->countAllResults() > 0) $errors[] = 'That username is already in use.';

        if ($isFaculty) {
            if ($authorName === '') $authorName = $fullName;
            if (! $departmentId || $db->table('mcq_tbl_departments')->where('department_id', $departmentId)->countAllResults() === 0) $errors[] = 'Select a valid faculty department.';
            if (! $subjectId || $db->table('mcq_tbl_subject')->where('subject_id', $subjectId)->where('department_id', $departmentId ?: 0)->countAllResults() === 0) $errors[] = 'Select a subject belonging to the selected department.';
            if ($designationRaw !== '' && $db->table('mcq_tbl_designations')->where('designation_id', (int) $designationRaw)->countAllResults() === 0) $errors[] = 'Select a valid designation.';
        }
        if ($errors) {
            $user = [
                'UserID' => $id ?: '', 'FullName' => $fullName, 'UserName' => $username, 'RollID' => $role,
                'author' => ['author_name' => $authorName, 'department_id' => $departmentId, 'designation_id' => $designationRaw, 'subject_id' => $subjectId],
            ];
            return $this->form($user, $errors);
        }

        $db->transBegin();
        try {
            if ($id) {
                $existing = $db->table('mcq_tbl_user')->where('UserID', $id)->get()->getRowArray();
                if (! $existing) throw new \RuntimeException('The account was not found.');
                $userData = ['FullName' => $fullName, 'UserName' => $username, 'RollID' => (int) $role];
                if ($password !== '') $userData['Password'] = password_hash($password, PASSWORD_DEFAULT);
                $db->table('mcq_tbl_user')->where('UserID', $id)->update($userData);
                $userId = (int) $id;
            } else {
                $next = $db->query('SELECT GREATEST((SELECT COALESCE(MAX(UserID),0) FROM mcq_tbl_user),(SELECT COALESCE(MAX(author_id),0) FROM mcq_tbl_author)) + 1 AS next_id')->getRowArray();
                $userId = (int) ($next['next_id'] ?? 0);
                if ($userId < 1) throw new \RuntimeException('Could not generate a new account ID.');
                $userData = ['UserID' => $userId, 'FullName' => $fullName, 'UserName' => $username, 'Password' => password_hash($password, PASSWORD_DEFAULT), 'RollID' => (int) $role];
                $db->table('mcq_tbl_user')->insert($userData);
            }

            if ($isFaculty) {
                $authorData = [
                    'author_name' => $authorName,
                    'designation_id' => $designationRaw === '' ? null : (int) $designationRaw,
                    'department_id' => (int) $departmentId,
                    'subject_id' => (int) $subjectId,
                    'RollId' => (int) $role,
                ];
                $authorExists = $db->table('mcq_tbl_author')->where('author_id', $userId)->countAllResults() > 0;
                if ($authorExists) {
                    $db->table('mcq_tbl_author')->where('author_id', $userId)->update($authorData);
                } else {
                    $db->table('mcq_tbl_author')->insert(['author_id' => $userId] + $authorData);
                }
            }
            // Existing faculty records are intentionally not deleted when a role is changed,
            // because MCQs may reference author_id. Prevent changing a faculty account to a non-faculty role.
            if ($id && ! $isFaculty && $db->table('mcq_tbl_author')->where('author_id', $userId)->countAllResults() > 0) {
                throw new \RuntimeException('This account has a faculty record linked to MCQs. Keep a faculty role; do not convert it to a non-faculty role.');
            }
            if (! $db->transStatus()) throw new \RuntimeException('Database transaction failed. No changes were committed.');
            $db->transCommit();
            return redirect()->to(site_url('master/admin/users'))->with('success', $id ? 'User account updated successfully.' : 'User account created successfully.');
        } catch (Throwable $e) {
            $db->transRollback();
            log_message('error', 'User account save failed: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Could not save the account: ' . $e->getMessage());
        }
    }

    public function toggleStatus(int $id)
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }
    
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to(site_url('master/admin/users'));
        }
    
        $db = db_connect();
    
        try {
            $user = $db->table('mcq_tbl_user')
                ->select('UserID, is_active')
                ->where('UserID', $id)
                ->get()
                ->getRowArray();
    
            if (!$user) {
                return redirect()->to(site_url('master/admin/users'))
                    ->with('error', 'User account not found.');
            }
    
            if (
                (int) $user['UserID'] === (int) (session()->get('user_id') ?? session()->get('UserID') ?? session()->get('userid') ?? 0)
                && (int) ($user['is_active'] ?? 1) === 1
            ) {
                return redirect()->to(site_url('master/admin/users'))
                    ->with('error', 'You cannot deactivate your own account.');
            }
    
            $newStatus = (int) ($user['is_active'] ?? 1) === 1 ? 0 : 1;
    
            $db->table('mcq_tbl_user')
                ->where('UserID', $id)
                ->update(['is_active' => $newStatus]);
    
            return redirect()->to(site_url('master/admin/users'))
                ->with(
                    'success',
                    $newStatus === 1
                        ? 'User account activated successfully.'
                        : 'User account deactivated successfully.'
                );
    
        } catch (\Throwable $e) {
            log_message(
                'error',
                'User status change failed: {message}',
                ['message' => $e->getMessage()]
            );
    
            return redirect()->to(site_url('master/admin/users'))
                ->with('error', 'Could not change account status. Check the application log.');
        }
    }
}
