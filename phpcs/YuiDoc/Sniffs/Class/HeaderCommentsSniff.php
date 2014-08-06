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
     * The tags which of only ONE of them should be present
     * 
     * @var array $relevantTags
     */
    protected $relevantTags = array(
        '@class' => false, 
        '@constructor' => false, 
        '@static' => false,
        '@extends' => false,
        '@namespace' => false
        );
    
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
        $this->initBeginOfBodyIndex($phpcsFile);
        
        // We are done here if we already are within the body
        if ($stackPtr > $this->beginOfBodyIndex) {
            
            return false;
        }
        
        // get all the tokens we are interested in
        $tokens = $phpcsFile->getTokens();

        // with the T_DOC_COMMENT_OPEN_TAG tag come the indexes of the other comment tags
        $commentTagIndexes = $tokens[$stackPtr]['comment_tags'];
        
        // now iterate over all the indexes and get the tokens stored within them
        foreach ($commentTagIndexes as $commentTagIndex) {
            
            // check if we got one of the header tags we want
            $tagContent = $tokens[$commentTagIndex]['content'];
            if (isset($this->relevantTags[$tagContent])) {
                
                $this->relevantTags[$tagContent] = true;
            }
        }
    }
    
    /**
     * Will init the index at which the class body begins
     * 
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found
     * 
     * @return void
     */
    protected function initBeginOfBodyIndex($phpcsFile) 
    {
        // only init if we did not do it before
        if (!isset($this->beginOfBodyIndex)) {
        
            $this->beginOfBodyIndex = $phpcsFile->findNext(
                    array(T_OPEN_PARENTHESIS), 
                    0
                    );
        }
    }
}
