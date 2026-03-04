<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GitHistoryController extends Controller
{
    /**
     * Get the git executable path
     */
    private function getGitPath()
    {
        // Windows paths to check
        $windowsPaths = [
            'C:\\Program Files\\Git\\cmd\\git.exe',
            'C:\\Program Files (x86)\\Git\\cmd\\git.exe',
            'C:\\Git\\cmd\\git.exe',
        ];
        
        // Unix paths to check
        $unixPaths = [
            '/usr/bin/git',
            '/usr/local/bin/git',
            '/opt/local/bin/git',
        ];
        
        $pathsToCheck = strtoupper(PHP_OS_FAMILY) === 'WINDOWS' ? $windowsPaths : $unixPaths;
        
        foreach ($pathsToCheck as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        // Try to find git in PATH using system command
        $command = strtoupper(PHP_OS_FAMILY) === 'WINDOWS' ? 'where git' : 'which git';
        $output = trim(shell_exec($command) ?? '');
        
        if (!empty($output) && file_exists($output)) {
            return $output;
        }
        
        return null;
    }
    /**
     * Get git log history
     */
    public function getHistory(Request $request)
    {
        $limit = $request->query('limit', 20);
        $format = $request->query('format', 'oneline');

        try {
            $projectPath = base_path();
            
            // Get git executable path (handle Windows and Unix)
            $gitPath = $this->getGitPath();
            if (!$gitPath) {
                return response()->json([
                    'success' => false,
                    'error' => 'Git is not installed or not found in system PATH',
                ], 500);
            }
            
            // Build git log command
            if ($format === 'detailed') {
                // Detailed format with author, date, and message
                $command = sprintf(
                    '"%s" -C %s log --oneline --decorate --date=short --pretty=format:"%%h|%%an|%%ad|%%s" -n %d',
                    $gitPath,
                    escapeshellarg($projectPath),
                    (int)$limit
                );
            } else {
                // Simple oneline format
                $command = sprintf(
                    '"%s" -C %s log --oneline -n %d',
                    $gitPath,
                    escapeshellarg($projectPath),
                    (int)$limit
                );
            }

            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output = trim($process->getOutput());
            $commits = [];

            if ($output) {
                $lines = explode("\n", $output);
                
                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;
                    
                    if ($format === 'detailed') {
                        $parts = explode('|', $line);
                        if (count($parts) >= 4) {
                            $commits[] = [
                                'hash' => trim($parts[0]),
                                'author' => trim($parts[1]),
                                'date' => trim($parts[2]),
                                'message' => trim($parts[3]),
                            ];
                        }
                    } else {
                        preg_match('/^([a-f0-9]+)\s+(.+)$/', $line, $matches);
                        if (!empty($matches)) {
                            $commits[] = [
                                'hash' => $matches[1],
                                'message' => $matches[2],
                            ];
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'commits' => $commits,
                'count' => count($commits),
            ]);
        } catch (\Exception $e) {
            \Log::error('Git history error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve git history: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get git commit details
     */
    public function getCommitDetails($hash)
    {
        try {
            $projectPath = base_path();
            $gitPath = $this->getGitPath();
            
            if (!$gitPath) {
                return response()->json([
                    'success' => false,
                    'error' => 'Git is not installed or not found',
                ], 500);
            }
            
            // Get detailed information about a specific commit
            $command = sprintf(
                '"%s" -C %s show --no-patch --pretty=format:"%%H|%%an|%%ae|%%ad|%%s|%%b" --date=iso %s',
                $gitPath,
                escapeshellarg($projectPath),
                escapeshellarg($hash)
            );

            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output = trim($process->getOutput());
            $parts = explode('|', $output, 6);

            return response()->json([
                'success' => true,
                'commit' => [
                    'hash' => $parts[0] ?? '',
                    'author' => $parts[1] ?? '',
                    'email' => $parts[2] ?? '',
                    'date' => $parts[3] ?? '',
                    'message' => $parts[4] ?? '',
                    'body' => $parts[5] ?? '',
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Git commit details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve commit details',
            ], 500);
        }
    }

    /**
     * Get git branch list
     */
    public function getBranches()
    {
        try {
            $projectPath = base_path();
            $gitPath = $this->getGitPath();
            
            if (!$gitPath) {
                return response()->json([
                    'success' => false,
                    'error' => 'Git is not installed or not found',
                ], 500);
            }
            
            // Get list of branches
            $command = sprintf(
                '"%s" -C %s branch -a --format="%(refname:short)|%(objectname:short)|%(creatordate:short)"',
                $gitPath,
                escapeshellarg($projectPath)
            );

            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output = trim($process->getOutput());
            $branches = [];

            if ($output) {
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    $parts = explode('|', $line);
                    if (count($parts) >= 2) {
                        $branches[] = [
                            'name' => trim($parts[0]),
                            'hash' => trim($parts[1]),
                            'date' => trim($parts[2] ?? ''),
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'branches' => $branches,
            ]);
        } catch (\Exception $e) {
            \Log::error('Git branches error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve branches',
            ], 500);
        }
    }

    /**
     * Get git status
     */
    public function getStatus()
    {
        try {
            $projectPath = base_path();
            $gitPath = $this->getGitPath();
            
            if (!$gitPath) {
                return response()->json([
                    'success' => false,
                    'error' => 'Git is not installed or not found',
                ], 500);
            }
            
            // Get current status
            $command = sprintf(
                '"%s" -C %s status --porcelain',
                $gitPath,
                escapeshellarg($projectPath)
            );

            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output = trim($process->getOutput());
            $changes = [];
            $stats = [
                'added' => 0,
                'modified' => 0,
                'deleted' => 0,
                'renamed' => 0,
                'untracked' => 0,
            ];

            if ($output) {
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;
                    
                    $status = substr($line, 0, 2);
                    $file = substr($line, 3);

                    $changes[] = [
                        'status' => $status,
                        'file' => $file,
                    ];

                    // Count by status
                    if (str_contains($status, 'A')) $stats['added']++;
                    if (str_contains($status, 'M')) $stats['modified']++;
                    if (str_contains($status, 'D')) $stats['deleted']++;
                    if (str_contains($status, 'R')) $stats['renamed']++;
                    if (str_contains($status, '?')) $stats['untracked']++;
                }
            }

            return response()->json([
                'success' => true,
                'changes' => $changes,
                'stats' => $stats,
                'hasChanges' => count($changes) > 0,
            ]);
        } catch (\Exception $e) {
            \Log::error('Git status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve git status',
            ], 500);
        }
    }
}
