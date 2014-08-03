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

        // did we get all of the required tags?
        foreach ($this->requiredTags as $requiredTag) {
            
            // if we got the entry but it is not set we have to create an error
            if ($this->relevantTags[$requiredTag] !== true) {
                
                $error = 'Missing required tag %s.';
                $phpcsFile->addError($error, $stackPtr, 'Found', array($requiredTag));              
            }
        }
        
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
        
        
        /*
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
    }*/
    }
}
