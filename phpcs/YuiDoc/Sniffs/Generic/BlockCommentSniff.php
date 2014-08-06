 <?php
/**
 * YuiDoc\Generic\BlockCommentSniff
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
 * This sniff ensures that for every comment block there is a block comment 
 * beginning it.
 * It is separated from the block tags by an empty line
 *
 * @category  JS
 * @package   PHP_CodeSniffer
 * @author    Bernhard Wick wick.b@hotmail.de
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/yui-doc-sniff
 */
class YuiDoc_Sniffs_Generic_BlockCommentSniff implements PHP_CodeSniffer_Sniff
{
    
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array $supportedTokenizers
     */
    public $supportedTokenizers = array('PHP', 'JS');

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
        
        // there should be a comment before the first tag/end of comment, so
        // we have to check where that might be
        if (!empty($commentTagIndexes)) {
            
            $nextElementIndex = $commentTagIndexes[0];
            
        } else {
            
            $nextElementIndex = $tokens[$stackPtr]['comment_closer'];
        }        
        
        // also get the index of the comment (if any) and check the relation
        $docCommentIndex = $phpcsFile->findNext(array(T_DOC_COMMENT_STRING), $stackPtr);
        if ($nextElementIndex < $docCommentIndex || $docCommentIndex === false) {
            
            $error = 'There must be a doc comment before the first tag/end of the block.';
            $phpcsFile->addError(
                    $error, 
                    $stackPtr, 
                    'Found', 
                    array()
                    ); 
                    
        } elseif (!empty($commentTagIndexes)) { 
        
            // there also should be an empty line in between comment and first tag
            $starCounter = 0;
            for ($i = $docCommentIndex; $i < $commentTagIndexes[0]; $i++) {

                // if we got a star we have to count it
                if ($tokens[$i]['code'] === T_DOC_COMMENT_STAR) {
                    
                    $starCounter ++;
                }
            }
        
            // anything else than 2 stars means there is no or mone than one empty line
            if ($starCounter !== 2) {
                
                $error = 'There must be exactly one empty line between the doc comment and the first tag.';
                $phpcsFile->addError(
                    $error, 
                    $stackPtr, 
                    'Found', 
                    array()
                    );
            }
        }
    }
}
