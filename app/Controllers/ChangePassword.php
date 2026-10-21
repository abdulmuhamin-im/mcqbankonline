<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class ChangePassword extends BaseController
{
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (! in_array((string) session()->get('role'), ['2', '3'], true)) {
            return redirect()->to(site_url('dashboard/admin'));
        }

        return view('auth/change_password', [
            'pageTitle' => 'Change Password — MCQ Bank',
            'errorMessage' => session()->getFlashdata('passwordError'),
            'successMessage' => session()->getFlashdata('passwordSuccess'),
        ]);
    }

    public function update()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (! in_array((string) session()->get('role'), ['2', '3'], true)) {
            return redirect()->to(site_url('dashboard/admin'));
        }

        $rules = [
            'current_password' => 'required|max_length[255]',
            'new_password' => 'required|min_length[8]|max_length[255]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('master/change-password'))
                ->with('passwordError', implode(' ', $this->validator->getErrors()));
        }

        $userId = (int) (session()->get('user_id') ?? 0);
        if ($userId < 1) {
            return redirect()->to(site_url('login'));
        }

        $db = db_connect();
        $user = $db->table('mcq_tbl_user')
            ->select('UserID, Password')
            ->where('UserID', $userId)
            ->get()->getRowArray();

        if (! $user) {
            return redirect()->to(site_url('master/change-password'))
                ->with('passwordError', 'Your user account could not be found. Please contact the administrator.');
        }

        $current = (string) $this->request->getPost('current_password');
        $stored = (string) ($user['Password'] ?? '');
        $info = password_get_info($stored);
        $isHash = isset($info['algoName']) && $info['algoName'] !== 'unknown';
        $currentMatches = $isHash ? password_verify($current, $stored) : hash_equals($stored, $current);

        if (! $currentMatches) {
            return redirect()->to(site_url('master/change-password'))
                ->with('passwordError', 'Current password is incorrect.');
        }

        if (hash_equals($current, (string) $this->request->getPost('new_password'))) {
            return redirect()->to(site_url('master/change-password'))
                ->with('passwordError', 'New password must be different from the current password.');
        }

        $ok = $db->table('mcq_tbl_user')->where('UserID', $userId)->update([
            'Password' => password_hash((string) $this->request->getPost('new_password'), PASSWORD_DEFAULT),
        ]);

        if (! $ok || $db->affectedRows() < 0) {
            log_message('error', 'Password update failed for UserID {id}.', ['id' => $userId]);
            return redirect()->to(site_url('master/change-password'))
                ->with('passwordError', 'Password could not be updated. Please try again.');
        }

        return redirect()->to(site_url('master/change-password'))
            ->with('passwordSuccess', 'Password changed successfully. Please use your new password next time you log in.');
    }
}
