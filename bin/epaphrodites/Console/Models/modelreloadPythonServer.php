<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Epaphrodites\epaphrodites\Console\Setting\settingreloadPythonServer;

/**
 * Model for managing Python server lifecycle (start, stop, reload)
 */
class modelreloadPythonServer extends settingreloadPythonServer
{
    private $shutdownInProgress = false;
    private $currentPid = null;
    private $output = null;

    /**
     * Sets up signal handlers for graceful shutdown
     */
    private function setupSignalHandlers()
    {
        // Check if pcntl extension is available
        if (!extension_loaded('pcntl')) {
            return false;
        }

        // Install signal handlers
        pcntl_signal(SIGINT, [$this, 'handleShutdownSignal']);
        pcntl_signal(SIGTERM, [$this, 'handleShutdownSignal']);
        
        return true;
    }

    /**
     * Signal handler for graceful shutdown
     */
    public function handleShutdownSignal($signal)
    {
        if ($this->shutdownInProgress) {
            return;
        }
        
        $this->shutdownInProgress = true;
        
        if ($this->output) {
            $this->output->writeln("");
            $this->output->writeln("<info>🛑 Python server shutdown signal received...</info>");
        }

        $this->forceStopServer();
        
        if ($this->output) {
            $this->output->writeln("<info>✅ Python server stopped successfully!</info>");
        }
    }

    /**
     * Force stop the Python server
     */
    private function forceStopServer()
    {
        $port = _PYTHON_SERVER_PORT_;

        if ($this->currentPid) {
            $this->stopPythonServer($this->currentPid, $this->output);
        }

        $this->killPythonServerByPort($port, $this->output);
    }

    /**
     * Execute method for Symfony Console command
     * Handles options -s (start), -r (reload), -k (kill)
     */
    protected function execute(
        InputInterface $input, 
        OutputInterface $output
    ): int{
        $this->output = $output;

        $start = $input->getOption('start');
        $reload = $input->getOption('reload');
        $kill = $input->getOption('kill');

        $optionsCount = ($start ? 1 : 0) + ($reload ? 1 : 0) + ($kill ? 1 : 0);
        if ($optionsCount > 1) {
            $output->writeln('<error>Error: Please specify only one option (-s, -r, or -k).</error>');
            return Command::FAILURE;
        }
        if ($optionsCount === 0) {
            $output->writeln('<error>Error: No option specified. Use -s (start), -r (reload), or -k (stop).</error>');
            return Command::FAILURE;
        }

        $this->setupSignalHandlers();

        if ($start) {
            $output->writeln("<info>🚀 Attempting to start Python server ...</info>");
            return $this->startServer($input, $output, false);
        } elseif ($reload) {
            $output->writeln("<info>🔄 Attempting to reload Python server ...</info>");
            return $this->reloadServer($input, $output);
        } elseif ($kill) {
            $output->writeln("<info>🛑 Attempting to stop Python server ...</info>");
            return $this->stopServer($output);
        }

        return Command::FAILURE;
    }

    /**
     * Method to start the Python server
     */
    public function startServer(
        InputInterface $input, 
        OutputInterface $output, 
        bool $allMsg = false
    ): int{
        $this->output = $output;
        $port = _PYTHON_SERVER_PORT_;
        $host = '127.0.0.1';
        $filePath = _PYTHON_FILE_FOLDERS_ . 'config/server.py';

        if ($this->isPythonServerRunning($port, $host, $output)) {
            $output->writeln("   └── Stop with:       <fg=gray>php heredia server -k</>");
            if($allMsg) {
                $output->writeln("");
                $output->writeln("🎉 <fg=cyan>Happy coding with Epaphrodites!</>");
                $output->writeln("");
                $output->writeln("💡 <comment>Press Ctrl+C to quit</comment>");
            }
            return Command::SUCCESS;
        }

        $result = $this->executePythonServer($filePath, $port, $host, true, $output, $allMsg);

        if (!$result['success']) {
            $output->writeln("<error>❌ Failed to launch Python server: {$result['error']}</error>");
            return Command::FAILURE;
        }

        $this->currentPid = $result['pid'];
        $attempts = 0;
        $maxAttempts = 10;

        while ($attempts < $maxAttempts && !$this->shutdownInProgress) {
            sleep(1);
            
            if (extension_loaded('pcntl')) {
                pcntl_signal_dispatch();
            }
            
            if ($this->isPythonServerRunning($port, $host)) {
                return Command::SUCCESS;
            }
            $attempts++;
        }

        if ($this->shutdownInProgress) {
            return Command::SUCCESS;
        }

        $output->writeln("<error>❌ Server did not respond after $maxAttempts attempts</error>");
        if ($result['pid']) {
            $this->stopPythonServer($result['pid'], $output);
        }

        return Command::FAILURE;
    }

    /**
     * Method to stop the Python server
     * 
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    public function stopServer(OutputInterface $output): int
    {
        $this->output = $output;
        $port = _PYTHON_SERVER_PORT_;
        $host = '127.0.0.1';

        if (!$this->isPythonServerRunning($port, $host, $output)) {
            $output->writeln("<comment>⚠️ No Python server running </comment>");
            return Command::SUCCESS;
        }

        $killResult = $this->killPythonServerByPort($port, $output);

        if ($killResult['success']) {
            $output->writeln("<info>✅ Python server stopped successfully!</info>");
            if (!empty($killResult['killed_pids'])) {
                $output->writeln("<comment>📋 Stopped PIDs: " . implode(', ', $killResult['killed_pids']) . "</comment>");
            }
            return Command::SUCCESS;
        } else {
            $output->writeln("<error>❌ Failed to stop Python server: {$killResult['message']}</error>");
            return Command::FAILURE;
        }
    }

    /**
     * Method to reload the Python server
     */
    public function reloadServer(InputInterface $input, OutputInterface $output): int
    {

        $this->output = $output;
        $output->writeln("<info>🔄 Reloading Python server</info>");

        $stopResult = $this->stopServer($output);
        if ($stopResult !== Command::SUCCESS) {
            return $stopResult;
        }

        return $this->startServer($input, $output);
    }

    /**
     * Executes the Python server.py in the context of a Symfony command
     * 
     * @param string $scriptPath Path to the server.py file
     * @param int $port Server port
     * @param string $host Server IP address (default 127.0.0.1)
     * @param bool $background Run in background (default true)
     * @param OutputInterface|null $output Symfony output interface (optional)
     * @param bool $allMsg Display all messages (default false)
     * @return array Execution result
    */
    protected function executePythonServer($scriptPath, $port, $host = '127.0.0.1', $background = true, $output = null, $allMsg = false) 
    {
        if (!file_exists($scriptPath)) {
            $error = "The file $scriptPath does not exist";
            $output->writeln("<error>$error</error>");
            return ['success' => false, 'error' => $error, 'output' => null, 'pid' => null];
        }

        if ($allMsg) {
            $output->writeln("   └── Stop with:       <fg=gray>php heredia server -k</>");
            $output->writeln("");
            $output->writeln("🎉 <fg=cyan>Happy coding with Epaphrodites!</>");
            $output->writeln("");
            $output->writeln("💡 <comment>Press Ctrl+C to quit</comment>");
        }

        $logFile = 'pythonServer.log';
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }


        $command = _PYTHON_." " . escapeshellarg($scriptPath) . " --host=" . escapeshellarg($host) . " --port=" . escapeshellarg($port);
        
        if ($background) {
            if (PHP_OS_FAMILY === 'Windows') {

                $command = $command . " >> " . escapeshellarg($logFile) . " 2>&1";
                $process = popen('start /B cmd /C "' . $command . '"', 'r');
                if ($process === false) {
                    $error = "Failed to launch Python server";
                    $output->writeln("<error>$error</error>");
                    return ['success' => false, 'error' => $error, 'output' => null, 'pid' => null];
                }

                pclose($process);

                sleep(1);

                $pidCommand = 'tasklist /FI "IMAGENAME eq python.exe" /FO CSV /NH';
                $pidOutput = [];
                exec($pidCommand, $pidOutput);
                $pid = null;
                foreach ($pidOutput as $line) {

                    $columns = str_getcsv($line);
                    if (count($columns) >= 2 && $columns[0] === 'python.exe' && is_numeric($columns[1])) {

                        $cmdLineCheck = 'wmic process where ProcessId=' . $columns[1] . ' get CommandLine';
                        $cmdOutput = [];
                        exec($cmdLineCheck, $cmdOutput);
                        foreach ($cmdOutput as $cmdLine) {
                            if (strpos($cmdLine, basename($scriptPath)) !== false) {
                                $pid = (int)$columns[1];
                                break 2;
                            }
                        }
                    }
                }

                $result = [
                    'success' => $pid !== null,
                    'error' => $pid === null ? "Could not retrieve PID for Python process" : null,
                    'output' => [],
                    'pid' => $pid,
                    'background' => true
                ];
            } else {

                $command = $command . " >> " . escapeshellarg($logFile) . " 2>&1 & echo $!";
                $output_array = [];
                $returnCode = 0;
                exec($command, $output_array, $returnCode);
                $pid = $background ? (int) end($output_array) : null;
                $result = [
                    'success' => $returnCode === 0,
                    'error' => $returnCode !== 0 ? "Error during execution (code: $returnCode)" : null,
                    'output' => $output_array,
                    'pid' => $pid,
                    'background' => $background
                ];
            }
        } else {

            $command = $command . " >> " . escapeshellarg($logFile) . " 2>&1";
            $output_array = [];
            $returnCode = 0;
            exec($command, $output_array, $returnCode);
            $pid = null;
            $result = [
                'success' => $returnCode === 0,
                'error' => $returnCode !== 0 ? "Error during execution (code: $returnCode)" : null,
                'output' => $output_array,
                'pid' => $pid,
                'background' => $background
            ];
        }

        if ($output && !$result['success']) {
            $output->writeln("<error>Launch failed: {$result['error']}</error>");
            $output->writeln("<comment>Check logs at: $logFile</comment>");
            $output->writeln("<comment>Output: " . implode("\n", $result['output']) . "</comment>");
        }

        return $result;
    }

    /**
     * Checks if the Python server is running
     * 
     * @param int $port Server port
     * @param string $host Server IP address
     * @param OutputInterface|null $output Symfony output interface (optional)
     * @param int $timeout Connection timeout in seconds (default 2)
     * @return bool True if the server responds, false otherwise
     */
    protected function isPythonServerRunning($port, $host = '127.0.0.1', $output = null, $timeout = 2) 
    {
        $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($connection) {
            fclose($connection);
            if ($output) {
               $output->writeln("🐍 <fg=green>Python Server</>:      ✅ <info>Running</info>");
            }
            return true;
        }
        
        if ($output) {
            $output->writeln("🐍 <fg=green>Python Server</>:      ✅ <info>Running</info>");
        }
        return false;
    }

    /**
     * Stops a Python process by its PID
     * 
     * @param int $pid Process PID
     * @param OutputInterface|null $output Symfony output interface (optional)
     * @return bool True if the process was stopped, false otherwise
     */
    protected function stopPythonServer($pid, $output = null) 
    {
        if (!$pid) {
            if ($output) {
                $output->writeln("<comment>No PID provided</comment>");
            }
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $command = "taskkill /F /PID " . escapeshellarg($pid);
        } else {
            $command = "kill " . escapeshellarg($pid);
        }

        $output_array = [];
        $returnCode = 0;
        exec($command, $output_array, $returnCode);
        
        $success = $returnCode === 0;
        if ($output) {
            if ($success) {
                $output->writeln("<comment>✅ Process $pid stopped</comment>");
            } else {
                $output->writeln("<error>❌ Unable to stop process $pid</error>");
            }
        }
        
        return $success;
    }
    
    /**
     * Finds and stops all Python processes using a specific port
     * 
     * @param int $port Port to free
     * @param OutputInterface|null $output Symfony output interface (optional)
     * @return array Operation result
     */
    protected function killPythonServerByPort( 
        int $port, 
        object|null $output = null
    ):array{
        if ($output) {
            $output->writeln("<info>Searching for processes using port $port...</info>");
        }
        
        if (PHP_OS_FAMILY === 'Windows') {
            $command = "netstat -ano | findstr :$port";
            $cmd_output = [];
            exec($command, $cmd_output);
            
            $pids = [];
            foreach ($cmd_output as $line) {
                if (preg_match('/\s+(\d+)$/', $line, $matches)) {
                    $pids[] = $matches[1];
                }
            }
            
            $killed = [];
            foreach (array_unique($pids) as $pid) {
                if ($this->stopPythonServer($pid, $output)) {
                    $killed[] = $pid;
                }
            }
            
            $result = [
                'success' => !empty($killed),
                'killed_pids' => $killed,
                'message' => empty($killed) ? "No processes found on port $port" : "Processes stopped: " . implode(', ', $killed)
            ];
            
            if ($output) {
                $output->writeln("<comment>{$result['message']}</comment>");
            }
            
            return $result;
        } else {
            $command = "lsof -ti:$port | xargs kill -9";
            $output_array = [];
            $returnCode = 0;
            exec($command, $output_array, $returnCode);
            
            $result = [
                'success' => $returnCode === 0,
                'killed_pids' => [],
                'message' => $returnCode === 0 ? "Processes on port $port stopped" : "No processes found or error"
            ];
            
            if ($output) {
                $output->writeln("<comment>{$result['message']}</comment>");
            }
            
            return $result;
        }
    }

    /**
     * Checks if a process is running by PID
     * 
     * @param int $pid Process ID
     * @return bool True if process is running, false otherwise
     */
    protected function isProcessRunning($pid)
    {
        if (!$pid) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $command = "tasklist /FI \"PID eq $pid\" /FO CSV /NH";
            $output = [];
            exec($command, $output, $returnCode);
            return $returnCode === 0 && !empty($output) && strpos($output[0], (string)$pid) !== false;
        } else {
            $command = "ps -p " . escapeshellarg($pid) . " > /dev/null 2>&1";
            exec($command, $output, $returnCode);
            return $returnCode === 0;
        }
    }
}