<?php

namespace Epaphrodites\epaphrodites\Console\Models;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Epaphrodites\epaphrodites\Console\Setting\AddServerConfig;
use InvalidArgumentException;
use RuntimeException;

class LunchServer extends AddServerConfig
{
    private const ERROR_PORT_IN_USE = 'The port %d is currently in use.❌';
    
    private $phpProcess = null;
    private $pythonServer = null;
    private $output = null;
    private $shutdownInProgress = false;

    /**
     * Validates if the port number is within the valid range.
     * @param int $port The port number to validate.
     * @return bool True if the port is valid.
     * @throws InvalidArgumentException If the port is invalid.
     */
    private function validatePort($port)
    {
        if (!is_numeric($port) || $port < 1 || $port > 65535) {
            throw new InvalidArgumentException('Invalid port number.');
        }
        return true;
    }

    /**
     * Sets up signal handlers for graceful shutdown
     */
    private function setupSignalHandlers(OutputInterface $output)
    {

        if (function_exists('pcntl_signal')) {
            pcntl_signal( SIGINT, function() use ($output){
                $this->shutdown($output);
                exit(0);
            });

            pcntl_signal( SIGTERM, function() use ($output){
                $this->shutdown($output);
                exit(0);
            });            
        }
    }

    private function shutdown(OutputInterface $output){

        if(_RUN_PYTHON_SERVER_== true){
            $pythonServer = new \Epaphrodites\epaphrodites\Console\Models\modelreloadPythonServer;
            $pythonServer->stopServer($output);
        }
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
            $this->output->writeln("<info>🛑 Shutdown signal received. Stopping all servers...</info>");
        }

        $this->stopAllServers();
        
        if ($this->output) {
            $this->output->writeln("<info>✅ All servers stopped successfully. Goodbye!</info>");
        }
        
        exit(0);
    }

    /**
     * Stops all running servers
     */
    private function stopAllServers()
    {

        if ($this->pythonServer && _RUN_PYTHON_SERVER_ == true) {
            if ($this->output) {
                $this->output->writeln("<comment>🐍 Stopping Python server...</comment>");
            }
            $this->pythonServer->stopServer($this->output);
        }

        if ($this->phpProcess && is_resource($this->phpProcess)) {
            if ($this->output) {
                $this->output->writeln("<comment>🔱 Stopping PHP server...</comment>");
            }
            
            $status = proc_get_status($this->phpProcess);
            if ($status['running']) {

                proc_terminate($this->phpProcess);
                
                sleep(1);
                
                $status = proc_get_status($this->phpProcess);
                if ($status['running']) {
                    proc_terminate($this->phpProcess, 9);
                }
            }
            
            proc_close($this->phpProcess);
            $this->phpProcess = null;
        }
    }

    /**
     * Executes the command to start the server.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ){
        $this->output = $output;
        $port = $input->getOption('port');
        $address = "127.0.0.1";
        
        try {
            $this->validatePort($port);

            if ($this->isPortInUse($port, $address)) {
                throw new RuntimeException(sprintf(self::ERROR_PORT_IN_USE, $port));
            }

            $this->setupSignalHandlers($output);

            $this->startServer($port, $address, $output, $input);

            return self::SUCCESS;

        } catch (InvalidArgumentException $e) {
            $output->writeln("<error>Invalid argument: " . $e->getMessage() . "</error>");
            return self::FAILURE;
        } catch (RuntimeException $e) {
            $output->writeln("<error>Runtime error: " . $e->getMessage() . "</error>");
            return self::FAILURE;
        }
    }

    /**
     * Start server by executing PHP built-in server command.
     * @param int $port The port number.
     */
    private function startServer(
        $port,
        $host,
        OutputInterface $output,
        InputInterface $input
    ){
        $output->writeln("╭─────────────────────────────────────────────────────────────╮");
        $output->writeln("│ 🔱  <info>Epaphrodites Framework — <fg=gray>Development Suite Booting...</></info>   │");
        $output->writeln("╰─────────────────────────────────────────────────────────────╯");
        $output->writeln("");
        $output->writeln("🚀 <fg=cyan>Launch Target</>:      <href=http://127.0.0.1:$port><fg=gray>http://127.0.0.1:$port</></>");
        $output->writeln("🎯 <fg=cyan>Mode</>:               <fg=gray>Development</>");
        $output->writeln("📦 <fg=cyan>Version</>:            <fg=gray>Epaphrodites v1.0.0</>");
        $output->writeln("");
        $output->writeln("🖥️ <fg=green>PHP Server</>:          ✅ <info>Running</info>");

        $output->writeln("");
        
        $logFile = _SERVER_LOG_;
        $command = "php -S $host:$port > $logFile 2>&1";
        $this->phpProcess = proc_open($command, [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes);
    
        if (!is_resource($this->phpProcess)) {
            throw new RuntimeException("Failed to start the server.");
        }
  
        if(_RUN_PYTHON_SERVER_ == true){
            $this->pythonServer = new \Epaphrodites\epaphrodites\Console\Models\modelreloadPythonServer;
            $this->pythonServer->startServer($input, $output, true);
        }

        if(_RUN_PYTHON_SERVER_ == false){
             $output->writeln("<comment>(Note: Python server not detected — running PHP only mode)</comment>");
        }
    
        while (proc_get_status($this->phpProcess)['running'] && !$this->shutdownInProgress) {

            if (extension_loaded('pcntl')) {
                pcntl_signal_dispatch();
            }
            
            usleep(100000);
        }

        if (!$this->shutdownInProgress) {
            $exitCode = proc_close($this->phpProcess);
            if ($exitCode !== 0) {
                throw new RuntimeException(sprintf("Server exited with code %d", $exitCode));
            }
            
            $output->writeln("");
            $output->writeln(sprintf("<info>Server stopped with exit code %d</info>", $exitCode));
        }
    }

    /**
     * Checks if the port is in use by executing a command based on the operating system.
     * @param int $port The port number.
     * @return bool True if the port is in use, false otherwise.
     * @throws RuntimeException If the command execution fails.
     */
    private function isPortInUse($port , $host)
    {
        $timeout = 1;
    
        $socket = @fsockopen($host, $port, $errorCode, $errorMessage, $timeout);
    
        if ($socket === false) {
            return false;
        }
    
        fclose($socket);
        return true;
    }
}