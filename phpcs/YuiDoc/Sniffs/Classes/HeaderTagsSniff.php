 <?php
/**
 * YuiDoc\Classes\HeaderTagsSniff
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
 * This sniff ensures that all needed class doc comment tags are present.
 * It also assures they are grouped right and do not contain eliminate each other
 *
 * @category  JS
 * @package   PHP_CodeSniffer
 * @author    Bernhard Wick wick.b@hotmail.de
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/yui-doc-sniff
 */
class YuiDoc_Sniffs_Classes_HeaderTagsSniff implements PHP_CodeSniffer_Sniff
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
    protected $requiredTags = array('@class');
    
    /**
     * The tags which of only ONE of them should be present
     * 
     * @var array $rivalingTags
     */
    protected $rivalingTags = array('@constructor', '@static');
    
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
        $this->initLastValuableIndex($phpcsFile);
        
        // We are done here if we already are within the body
        if ($stackPtr > $this->lastValuableIndex) {
            
            return false;
        }
        
        // get all the tokens we are interested in
        $tokens = $phpcsFile->getTokens();

        // with the T_DOC_COMMENT_OPEN_TAG tag come the indexes of the other comment tags
        $commentTagIndexes = $tokens[$stackPtr]['comment_tags'];
        $closingTagIndex = $tokens[$stackPtr]['comment_closer'];
        
        // now iterate over all the indexes and get the tokens stored within them
        foreach ($commentTagIndexes as $commentTagIndex) {
            
            // check if we got one of the header tags we want
            $tagContent = $tokens[$commentTagIndex]['content'];
            if (isset($this->relevantTags[$tagContent])) {
                
                $this->relevantTags[$tagContent] = true;
            }
        }

        // did we get all of the required tags?
        foreach ($this->requiredTags as $requiredTag) {
            
            // if we got the entry but it is not set we have to create an error
            if ($this->relevantTags[$requiredTag] !== true) {
                
                $error = 'Missing required tag %s.';
                $phpcsFile->addError($error, $stackPtr, 'Found', array($requiredTag));              
            }
        }
        
        /**
         * Process rivaling tags here if there are any
         */
        if (!empty($this->rivalingTags)) {
            // are there doubled rivaling tags?
            $counter = 0;
            foreach ($this->rivalingTags as $rivalingTag) {

                // if we got the entry we have to increment the counter
                if ($this->relevantTags[$rivalingTag] === true) {

                    $counter ++;              
                }

                // if the counter is higher than 1 we got a problem
                if ($counter > 1) {

                    $error = 'Got several of the rivaling tags %s. '
                            . 'There should be only one.';
                    $phpcsFile->addError(
                            $error, 
                            $stackPtr, 
                            'Found', 
                            array(implode(', ', $this->rivalingTags))
                            );              
                }
            }

            // there should be at least ONE of the rivaling tags
            if ($counter === 0) {

                $error = 'You need at least one of these tags: %s.';
                $phpcsFile->addError(
                        $error, 
                        $stackPtr, 
                        'Found', 
                        array(implode(', ', $this->rivalingTags))
                        );    
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
    protected function initLastValuableIndex($phpcsFile) 
    {
        // only init if we did not do it before
        if (!isset($this->lastValuableIndex)) {
        
            $this->lastValuableIndex = $phpcsFile->findNext(
                    array(T_OPEN_PARENTHESIS), 
                    0
                    );
        }
    }
}
