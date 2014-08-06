 <?php
/**
 * YuiDoc\Methods\HeaderTagsSniff
 *
 * PHP version 5
 *
 * @category  JS
 * @package   PHP_CodeSniffer
 * @author    Bernhard Wick wick.b@hotmail.de
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/yui-doc-sniff
 */

/**
 * This sniff ensures that all needed methods doc comment tags are present.
 * It also assures they are grouped right and do not contain eliminate each other
 *
 * @category  JS
 * @package   PHP_CodeSniffer
 * @author    Bernhard Wick wick.b@hotmail.de
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/yui-doc-sniff
 */
class YuiDoc_Sniffs_Methods_HeaderTagsSniff extends YuiDoc_Sniffs_Classes_HeaderTagsSniff implements PHP_CodeSniffer_Sniff
{
    
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array $supportedTokenizers
     */
    public $supportedTokenizers = array('JS');
    
    /**
     * Marks the begin of the classes body so we do not try to 
     * analyze comment blocks within the class
     *
     * @var integer $lastValuableIndex
     */
    protected $lastValuableIndex;

    /**
     * The tags which MUST be present
     * 
     * @var array $neededTags
     */
    protected $requiredTags = array('@method', '@return');
    
    /**
     * The tags which of only ONE of them should be present
     * 
     * @var array $rivalingTags
     */
    protected $rivalingTags = array();
    
    /**
     * The tags which of only ONE of them should be present
     * 
     * @var array $relevantTags
     */
    protected $relevantTags = array(
        '@method' => false, 
        '@param' => false, 
        '@return' => false
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
        // if this is indeed a method doc block we have to invoke the parent
        if ($this->isRelevantDocBlock($phpcsFile, $stackPtr)) {
            
            parent::process($phpcsFile, $stackPtr);
        }
    }
    
    /**
     * We have to keep searching until the end of the file, so set the 
     * "body begin" accordingly to use the parent implementation
     * 
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found
     * 
     * @return void
     */
    protected function initLastValuableIndex($phpcsFile) 
    {
        // only init if we did not do it before
        if (!isset($this->lastValuableIndex)) {
        
            $this->lastValuableIndex = count($phpcsFile->getTokens()) - 1;
        }
    }
    
    /**
     * Determine if the found doc block is of any relevance for this sniff
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return boolean
     */
    protected function isRelevantDocBlock(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        return true;
    }
}
