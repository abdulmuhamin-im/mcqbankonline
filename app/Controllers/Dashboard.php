<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;

class Dashboard extends BaseController
{
    public function admin()
    {
        if (! in_array((string) session()->get('role'), ['0', '1'], true)) {
            return redirect()->to(site_url('dashboard/member'));
        }

        // The admin view reads the variable named adminFullName.
        $fullName = trim((string) (
            session()->get('full_name')
            ?? session()->get('fullName')
            ?? session()->get('name')
            ?? session()->get('username')
            ?? ''
        ));

        return view('dashboard/admin', [
            'pageTitle' => 'MCQ Bank — Administration',
            'adminFullName' => $fullName !== '' ? $fullName : (string) session()->get('username', 'Administrator'),
        ]);
    }

    public function member()
    {
        if (! in_array((string) session()->get('role'), ['2', '3'], true)) {
            return redirect()->to(site_url('dashboard/admin'));
        }

        $profile = [
            'fullName' => '',
            'designation' => '',
            'department' => '',
        ];

        // UserID and author_id correspond in the current database mapping.
        // Read faculty identity from the author record and its linked lookup tables.
        $userId = session()->get('user_id') ?? session()->get('UserID');

        if ($userId !== null && $userId !== '') {
            try {
                $row = db_connect()->table('mcq_tbl_author a')
                    ->select('a.author_name, d.designation_name, dep.department_name')
                    ->join('mcq_tbl_designations d', 'd.designation_id = a.designation_id', 'left')
                    ->join('mcq_tbl_departments dep', 'dep.department_id = a.department_id', 'left')
                    ->where('a.author_id', (int) $userId)
                    ->get()
                    ->getRowArray();

                if ($row) {
                    $profile['fullName'] = (string) ($row['author_name'] ?? '');
                    $profile['designation'] = (string) ($row['designation_name'] ?? '');
                    $profile['department'] = (string) ($row['department_name'] ?? '');
                }
            } catch (DatabaseException $e) {
                log_message('error', 'Faculty profile lookup failed: {message}', ['message' => $e->getMessage()]);
            }
        }

        return view('dashboard/member', [
            'pageTitle' => 'MCQ Bank — Teaching Faculty',
            'roleLabel' => (string) session()->get('role') === '2' ? 'Submitter' : 'Reviewer',
            'facultyProfile' => $profile,
        ]);
    }
}
