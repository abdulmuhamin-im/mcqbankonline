<?php

namespace App\Controllers;

class DatabaseBackup extends BaseController
{
    public function download()
    {
        // Require a logged-in user.
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        // Keep the existing role policy. Adjust these values only if your
        // application's actual role IDs differ.
        if (! in_array((string) session()->get('role'), ['0', '1'], true)) {
            return redirect()
                ->to(site_url('dashboard/member'))
                ->with('error', 'Administrator access is required.');
        }

        $db = db_connect();

        try {
            $lines = [
                '-- MCQBank database backup',
                '-- Generated: ' . date('Y-m-d H:i:s'),
                '-- Database: ' . $db->getDatabase(),
                '',
                'SET NAMES utf8mb4;',
                'SET FOREIGN_KEY_CHECKS=0;',
                '',
            ];

            $tables = $db->listTables();
            if (empty($tables)) {
                throw new \RuntimeException('No tables were found in the connected database.');
            }

            foreach ($tables as $table) {
                $escapedTable = $db->escapeIdentifiers($table);

                // Export table structure without destructive DROP statements.
                $createResult = $db->query('SHOW CREATE TABLE ' . $escapedTable);
                if ($createResult === false) {
                    throw new \RuntimeException('Could not read table structure for: ' . $table);
                }

                $createRow = $createResult->getRowArray();
                if (! is_array($createRow) || count($createRow) < 2) {
                    throw new \RuntimeException('Unexpected SHOW CREATE TABLE result for: ' . $table);
                }

                $createSql = array_values($createRow)[1];
                $createSql = preg_replace('/^CREATE TABLE\\s+/i', 'CREATE TABLE IF NOT EXISTS ', $createSql, 1);

                $lines[] = '';
                $lines[] = '-- Structure for table ' . $table;
                $lines[] = $createSql . ';';
                $lines[] = '';

                $fields = $db->getFieldNames($table);
                $rows = $db->query('SELECT * FROM ' . $escapedTable);
                if ($rows === false) {
                    throw new \RuntimeException('Could not read rows from table: ' . $table);
                }

                $records = $rows->getResultArray();
                if (empty($records)) {
                    continue;
                }

                $columnSql = implode(', ', array_map(
                    static fn ($field) => '`' . str_replace('`', '``', $field) . '`',
                    $fields
                ));

                $lines[] = '-- Data for table ' . $table;
                foreach ($records as $record) {
                    $values = [];
                    foreach ($fields as $field) {
                        $value = $record[$field] ?? null;
                        if ($value === null) {
                            $values[] = 'NULL';
                        } elseif (is_string($value) && function_exists('mb_check_encoding') && ! mb_check_encoding($value, 'UTF-8')) {
                            $values[] = '0x' . bin2hex($value);
                        } else {
                            $values[] = $db->escape($value);
                        }
                    }
                    $lines[] = 'INSERT INTO ' . $escapedTable . ' (' . $columnSql . ') VALUES (' . implode(', ', $values) . ');';
                }
                $lines[] = '';
            }

            $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';
            $lines[] = '';
            $sql = implode("\r\n", $lines);
            $filename = 'mcqbank_backup_' . date('Y-m-d_H-i-s') . '.sql';

            return $this->response
                ->download($filename, $sql)
                ->setContentType('application/sql; charset=utf-8');
        } catch (\Throwable $e) {
            log_message('error', 'Database backup failed: {message}', ['message' => $e->getMessage()]);
            return redirect()
                ->to(site_url('dashboard/admin'))
                ->with('error', 'Database backup failed. Check writable/logs for details.');
        }
    }
}
