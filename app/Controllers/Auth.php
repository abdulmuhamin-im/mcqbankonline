<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return $this->redirectForRole((string) session()->get('role'));
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('auth/login');
        }

        $rules = [
            'UserName' => 'required|max_length[255]',
            'Password' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return view('auth/login', [
                'validation' => $this->validator,
                'loginError' => 'براہِ کرم درست username اور password درج کریں۔',
            ]);
        }

        $username = trim((string) $this->request->getPost('UserName'));
        $password = (string) $this->request->getPost('Password');

        $userModel = new UserModel();
        $user = $userModel->where('UserName', $username)->first();

        if (
            ! $user
            || (int) ($user['is_active'] ?? 1) !== 1
            || ! $this->verifyAndUpgradePassword($user, $password, $userModel)
        ) {
            return view('auth/login', [
                'loginError' => 'Username یا password درست نہیں ہے۔',
            ]);
        }

        session()->regenerate(true);
        // Read the account's display name from whichever supported Full Name column exists.
        $fullName = trim((string) (
            $user['FullName'] ?? $user['full_name'] ?? $user['Full_Name'] ?? $user['fullName'] ?? ''
        ));

        session()->set([
            'user_id'    => (int) $user['UserID'],
            'username'   => (string) $user['UserName'],
            'full_name'  => $fullName,
            'role'       => (string) $user['RollID'],
            'isLoggedIn' => true,
        ]);

        return $this->redirectForRole((string) $user['RollID']);
    }

    private function verifyAndUpgradePassword(array $user, string $password, UserModel $userModel): bool
    {
        $stored = (string) ($user['Password'] ?? '');

        // Accept modern hashes, and safely upgrade a matching legacy plaintext password.
        if (password_get_info($stored)['algo'] !== null && password_get_info($stored)['algo'] !== 0) {
            return password_verify($password, $stored);
        }

        if (! hash_equals($stored, $password)) {
            return false;
        }

        $userModel->update((int) $user['UserID'], [
            'Password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return true;
    }

    private function redirectForRole(string $role)
    {
        if (in_array($role, ['0', '1'], true)) {
            return redirect()->to(site_url('dashboard/admin'));
        }

        if (in_array($role, ['2', '3'], true)) {
            return redirect()->to(site_url('dashboard/member'));
        }

        session()->destroy();
        return redirect()->to(site_url('login'))->with('error', 'اس صارف کا role درست نہیں ہے۔');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }
}
