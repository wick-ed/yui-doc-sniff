 <?php
/**
 * YuiDoc\Files\AtLeastOneModuleSniff
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Bernhard Wick wick.b@hotmail.de
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/yui-doc-sniff
 */

/**
 * This sniff ensures that there is at least one @module tag per file
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Bernhard Wick wick.b@hotmail.de
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/yui-doc-sniff
 */
class YuiDoc_Sniffs_Files_AtLeastOneModuleSniff implements PHP_CodeSniffer_Sniff
{
    
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array $supportedTokenizers
     */
    public $supportedTokenizers = array('JS');
    
    /**
     * Counter to keep track of the occurences of the @module tag
     * 
     * @var int $counter
     */
    protected $counter = 0;

    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array
     */
    public function register()
    {
        return array(T_DOC_COMMENT);
    }

    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // get all the tokens we are interested in
        $tokens = $phpcsFile->getTokens();
        
        // check if we got the @module tag, if so increment the counter
        if (strpos($tokens[$stackPtr]['content'], '@module') !== false) {
            
            $this->counter ++;
        }
        
        // if we reached the end and got a count of 0 we will throw an error
        if (($stackPtr === count($tokens) - 1) && $this->counter === 0) {
            
            $error = 'The @module tag seems to be missing. It is needed at least once.';
            $phpcsFile->addError($error, $stackPtr, 'Found', array());
        }
    }
}
