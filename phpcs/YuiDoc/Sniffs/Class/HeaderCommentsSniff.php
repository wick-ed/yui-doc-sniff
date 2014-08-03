 <?php
/**
 * YuiDoc\Files\HeaderCommentsSniff
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
 * This sniff ensures that class name mentioned behind @class matches the actual class name
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Bernhard Wick wick.b@hotmail.de
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/yui-doc-sniff
 */
class YuiDoc_Sniffs_Class_HeaderCommentsSniff implements PHP_CodeSniffer_Sniff
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
        return array(T_DOC_COMMENT_OPEN_TAG);
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

        // collect the whole comment
        $docComment = '';
        for ($i = $stackPtr; $i < count($tokens); $i++) {

            // if we reached the end tag we can break, otherwise we collect what we got
            if ($tokens[$i]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {

                $docComment .= $tokens[$i]['content'];

            } else {

                break;
            }
        }
error_log(var_export($docComment, true));
        // if we get the class name we have to check for the actual classname
        if (strpos($docComment, '@class') !== false) {

            // iterate over the next tokens until we get a string, that should be the class name
            $suggestedClassNames = array();
            preg_match('/@class\s(.+)\r/', $docComment, $suggestedClassNames);
            $suggestedClassName = array_pop($suggestedClassNames);

            // now we have to get up to the class tag and get the next string from there
            $tmp = $phpcsFile->findNext(T_STRING, $stackPtr);
            if (is_string($tmp)) {

                $actualClassName = $phpcsFile->findNext(T_STRING, $stackPtr);

                // if they do not match we can throw a warning
                if ($suggestedClassName !== $actualClassName) {

                    $error = 'Mismatch of suggested (%s) and actual (%s) class name.';
                    $phpcsFile->addError($error, $stackPtr, 'Found', array($suggestedClassName, $actualClassName));
                }
            }
        }
    }
}
