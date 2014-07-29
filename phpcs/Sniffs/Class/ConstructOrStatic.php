 <?php
/**
 * YuiDoc\Files\ConstructOrStatic
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
 * This sniff ensures that there is either a @constructor or @static tag beneath the @class tag
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Bernhard Wick wick.b@hotmail.de
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/yui-doc-sniff
 */
class YuiDoc_Sniffs_Class_ConstructOrStatic implements PHP_CodeSniffer_Sniff
{
    
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array $supportedTokenizers
     */
    public $supportedTokenizers = array('PHP', 'JS',);
    
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
     * @return void|boolean
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // get all the tokens we are interested in
        $tokens = $phpcsFile->getTokens();

        // check which comes first, @class or one of the others
        $classIndex = strpos($tokens[$stackPtr]['content'], '@class');

        // if we do not even have a @class tag we can move on
        if ($classIndex === false) {

            return false;
        }

        // check the others
        $staticIndex = strpos($tokens[$stackPtr]['content'], '@static');
        $constructorIndex = strpos($tokens[$stackPtr]['content'], '@constructor');

        // if both are false there is an error, if both are not there is as well
        if ($staticIndex === false && $constructorIndex === false) {

            $error = 'There is neither a @static nor a @constructor tag near @class tag.Use one.';
            $phpcsFile->addError($error, $stackPtr, 'Found', array());

        } elseif ($staticIndex !== false && $constructorIndex !== false) {

            $error = 'There is both a @static and a @constructor tag near @class tag. Use one.';
            $phpcsFile->addError($error, $stackPtr, 'Found', array());

        } elseif ($classIndex > ((int) $staticIndex + (int) $constructorIndex)) {
            // if any of the other tags are in front of the @class tag we will create a warning

            $warning = 'The @class tag should be in front of any @static or @constructor tag.';
            $phpcsFile->addWarning($warning, $stackPtr, 'Found', array());
        }
    }
}
