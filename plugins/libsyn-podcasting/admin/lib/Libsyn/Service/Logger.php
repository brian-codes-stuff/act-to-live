<?php
namespace Libsyn\Service;
/*
	This class is designed to handle the debug logger logging.
*/
use DateTime;
use RuntimeException;
use Libsyn\Service\Psr\Log\AbstractLogger;
use LIbsyn\Service\Psr\Log\LogLevel;

class Logger extends AbstractLogger {

	// use \Libsyn\Service;

	/**
	 * Logger options (not like core php logging)
	 * @var array
	 */
	protected $options = array (
		'extension'      => 'log',
		'dateFormat'     => 'Y-m-d G:i:s.u',
		'filename'       => false,
		'flushFrequency' => true,
		'prefix'         => 'log_',
		'logFormat'      => false,
		'appendContext'  => true,
	);

	/**
	 * Path to the log file
	 * @var string
	 */
	public $logFilePath;

	/**
	 * Current minimum logging threshold
	 * @var int
	 */
	protected $logLevelThreshold = LogLevel::DEBUG;

    	/**
    	 * The number of lines logged in this instances lifetime
    	 * @var int
    	 */
 	private $logLineCount = 0;

    	/**
    	 * Log levels
    	 * @var type
    	 */
	protected $logLevels = array(
		LogLevel::EMERGENCY => 0,
		LogLevel::ALERT     => 1,
		LogLevel::CRITICAL  => 2,
		LogLevel::ERROR     => 3,
		LogLevel::WARNING   => 4,
		LogLevel::NOTICE    => 5,
		LogLevel::INFO      => 6,
		LogLevel::DEBUG     => 7
	);

    	/**
    	 * This holds the file handle for the log file
    	 * @var resource
    	 */
	private $fileHandle;

	/**
	 * This holds the last line logged to the logger
	 * @var string
	 */
	private $lastLine = '';

	/**
	 * Octal notation for the default permissions of the log file
	 * @var int
	 */
	private $defaultPermissions = 0777;

    	/**
    	 * Class Constructor
    	 * @param string $logDirectory 
    	 * @param string $logLevelThreshold 
    	 * @param array $options 
    	 * 
    	 * @internal param string $logFilePrefix The prefix for the log file name
    	 *  @internal param string $logFileExt The extension for the log file
    	 */
	public function __construct($logDirectory, $logLevelThreshold = LogLevel::DEBUG, array $options = array()) {
		$this->logLevelThreshold = $logLevelThreshold;
		$this->options = array_merge($this->options, $options);
		$this->hasLogger = true;
		$this->logger = $this;

		$logDirectory = rtrim($logDirectory, DIRECTORY_SEPARATOR);
		if ( ! file_exists($logDirectory)) {
			mkdir($logDirectory, $this->defaultPermissions, true);
		}

		if(strpos($logDirectory, 'php://') === 0) {
			$this->setLogToStdOut($logDirectory);
			$this->setFileHandle('w+');
		} else {
			$this->setLogFilePath($logDirectory);
			if(file_exists($this->logFilePath) && !is_writable($this->logFilePath)) {
				throw new RuntimeException('The file could not be written to. Check that appropriate permissions have been set.');
			}
			$this->setFileHandle('a');
		}

		if ( ! $this->fileHandle) {
			throw new RuntimeException('The file could not be opened. Check permissions.');
		}
	}

    	/**
    	 * Sets the Log outuput
    	 * @param string $stdOutPath 
    	 * @return type
    	 */
	public function setLogToStdOut($stdOutPath) {
		$this->logFilePath = $stdOutPath;
	}

    /**
     * @param string $logDirectory
     */
    	/**
    	 * Sets the log file path
    	 * @param string $logDirectory 
    	 * @return type
    	 */
	public function setLogFilePath($logDirectory) {
		if ($this->options['filename']) {
			if (strpos($this->options['filename'], '.log') !== false || strpos($this->options['filename'], '.txt') !== false) {
				$this->logFilePath = $logDirectory.DIRECTORY_SEPARATOR.$this->options['filename'];
			} else {
				$this->logFilePath = $logDirectory.DIRECTORY_SEPARATOR.$this->options['filename'].'.'.$this->options['extension'];
			}
		} else {
			if(file_exists($logDirectory)){
				$this->logFilePath = $logDirectory;
			} else {
				$this->logFilePath = $logDirectory.DIRECTORY_SEPARATOR.$this->options['prefix'].date('Y-m-d').'.'.$this->options['extension'];
			}
		}
	}

    	/**
    	 * Sets the File handler
    	 * @param string $writeMode 
    	 * @internal param resource $fileHandle
    	 */
	public function setFileHandle($writeMode) {
		$this->fileHandle = fopen($this->logFilePath, $writeMode);
	}

    	/**
    	 * Class destructor
    	 * @return type
    	 */
	public function __destruct() {
		if ((null !== $this->fileHandle) && is_resource($this->fileHandle)) {
			fclose($this->fileHandle);
		}
	}

    	/**
    	 * Sets the date format used by all instance of the Logger
    	 * @param string $dateFormat 
    	 * @return type
    	 */
	public function setDateFormat($dateFormat) {
		$this->options['dateFormat'] = $dateFormat;
	}

    	/**
    	 * Sets the Log Level Threshold
    	 * @param string $logLevelThreshold 
    	 * @return type
    	 */
	public function setLogLevelThreshold($logLevelThreshold) {
		$this->logLevelThreshold = $logLevelThreshold;
	}

    	/**
    	 * Logs with an arbitrary level
    	 * @param mixed $level 
    	 * @param string $message 
    	 * @param array $context 
    	 * @return null
    	 */
	public function log($level, $message, array $context = array()) {
		if ($this->logLevels[$this->logLevelThreshold] < $this->logLevels[$level]) {
			return;
		}
		$message = $this->formatMessage($level, $message, $context);
		$this->write($message);
	}

    /**
     * Writes a line to the log without prepending a status or timestamp
     *
     * @param string $message Line to write to the log
     * @return void
     */
    	/**
    	 * Writes a line to the log without prepending a status or timestamp
    	 * @param string $message Line to write to the log
    	 * @return void
    	 */
	public function write($message) {
		if (null !== $this->fileHandle) {
			if (is_resource($this->fileHandle) && fwrite($this->fileHandle, $message) === false) {
				throw new RuntimeException('The file could not be written to. Check that appropriate permissions have been set.');
			} else {
				$this->lastLine = trim($message);
				$this->logLineCount++;

				if ($this->options['flushFrequency'] && $this->logLineCount % $this->options['flushFrequency'] === 0 ) {
					fflush($this->fileHandle);
				}
			}
		}
	}

    	/**
    	 * Get the file path that the log is currently writing to
    	 * @return string
    	 */
	public function getLogFilePath() {
		return $this->logFilePath;
	}

    	/**
    	 * Get the last line logged to the log file
    	 * @return string
    	 */
	public function getLastLogLine() {
		return $this->lastLine;
	}

    	/**
    	 * Formats the message for logging
    	 * @param string $level The log level of the message
    	 * @param string $message The message to the log
    	 * @param array $context The context
    	 * @return string
    	 */
	protected function formatMessage($level, $message, $context) {
		if ($this->options['logFormat']) {
			$parts = array(
				'date'          => $this->getTimestamp(),
				'level'         => strtoupper($level),
				'level-padding' => str_repeat(' ', 9 - strlen($level)),
				'priority'      => $this->logLevels[$level],
				'message'       => $message,
				'context'       => json_encode($context),
			);
			$message = $this->options['logFormat'];
			foreach ($parts as $part => $value) {
				$message = str_replace('{'.$part.'}', $value, $message);
			}

		} else {
			$message = "[{$this->getTimestamp()}] [{$level}] {$message}";
		}

		if ($this->options['appendContext'] && ! empty($context)) {
			$message .= PHP_EOL.$this->indent($this->contextToString($context));
		}

		return $message.PHP_EOL;
	}

    	/**
    	 * Gets the correctly formatted Date/Time for the log entry
    	 * 
    	 * Fixes php DateTime non ability to get microseconds, to work correctly here it is
    	 * @return string
    	 */
	private function getTimestamp() {
		$originalTime = microtime(true);
		$micro = sprintf("%06d", ($originalTime - floor($originalTime)) * 1000000);
		$date = new DateTime(date('Y-m-d H:i:s.'.$micro, $originalTime));
		return $date->format($this->options['dateFormat']);
	}

    	/**
    	 * Takes the given context and converts it to a string
    	 * @param array $context 
    	 * @return string
    	 */
	protected function contextToString($context) {
		$export = '';
		foreach ($context as $key => $value) {
			$export .= "{$key}: ";
			$export .= preg_replace(array(
				'/=>\s+([a-zA-Z])/im',
				'/array\(\s+\)/im',
				'/^  |\G  /m'
			), array(
				'=> $1',
				'array()',
				'    '
			), str_replace('array (', 'array(', var_export($value, true)));
			$export .= PHP_EOL;
		}
		return str_replace(array('\\\\', '\\\''), array('\\', '\''), rtrim($export));
	}

    	/**
    	 * Indents the given string with the given indent
    	 * @param string $string 
    	 * @param string $indent 
    	 * @return string
    	 */
	protected function indent($string, $indent = '    ') {
		return $indent.str_replace("\n", "\n".$indent, $string);
	}

}