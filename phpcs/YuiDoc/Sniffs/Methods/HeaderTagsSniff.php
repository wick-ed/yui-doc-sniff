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
        // if this is not relevant for us we will pass
        if (!$this->isRelevantDocBlock($phpcsFile, $stackPtr)) {
        
            return false;
        }    
        
        // invoke parent implementation otherwise
        parent::process($phpcsFile, $stackPtr);

        // invoke the postProcess hook
        $this->postProcess($phpcsFile, $stackPtr);
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
        // only if there is a T_FUNCTION token we are where we want to be
        $functionIndex = $phpcsFile->findNext(array(T_FUNCTION), $stackPtr);
        
        // something that might state the end of declaration is a comma or
        // an closing curly bracket
        $declarationEndIndex = $phpcsFile->findNext(
                array(T_COMMA, T_CLOSE_CURLY_BRACKET),
                $stackPtr
                );
        
        // if we got a function keyword and it occurend in the right place
        if ($functionIndex !== false && $functionIndex < $declarationEndIndex) {
            
            return true;
        }
        
        // still here? Seems we did not fing anything
        return false;
    }
    
    /**
     * Hook which allows to detach specific tests from more generic ones.
     * This enables us to easily extend these classes without getting all of 
     * their behaviour
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void|boolean
     */
    protected function postProcess(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        /**
         * Test for params or the need for them
         */
        $tokens = $phpcsFile->getTokens();
        
        // get the indexes of the brackets
        $bracketOpenIndex = $phpcsFile->findNext(array(T_OPEN_PARENTHESIS), $stackPtr);
        $bracketCloseIndex = $tokens[$bracketOpenIndex]['parenthesis_closer'];
        
        // if there are params we have to check how many
        $expectationCounter = -1;
        $tmp = $stackPtr;
        do {
            
            $expectationCounter ++;
            $tmp = $phpcsFile->findNext(array(T_STRING), $tmp + 1);
            
        } while ($tmp !== false && $tmp < $bracketCloseIndex);
        
        // now get the number of @param tags from the doc block and compare
        $commentTagsIndexes = $tokens[$stackPtr]['comment_tags'];
        $actualCounter = 0;
        foreach ($commentTagsIndexes as $commentTagsIndex) {
            
            if ($tokens[$commentTagsIndex]['content'] === '@param') {
                
                $actualCounter ++;
            }
        }
        
        // if the counters do not match we got an error
        if ($expectationCounter !== $actualCounter) {
            
            $error = 'Mismatching @param tag count. Expected %s but got %s.';
            $phpcsFile->addError(
                    $error, 
                    $stackPtr, 
                    'Found',
                    array($expectationCounter, $actualCounter)
                    );
        }
    }
}
