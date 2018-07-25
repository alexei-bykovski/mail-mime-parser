<?php
/**
 * This file is part of the ZBateson\MailMimeParser project.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace ZBateson\MailMimeParser\Header\Part;

use ZBateson\StreamDecorators\Util\CharsetConverter;
use DateTime;

/**
 * Parses a header into a DateTime object.
 *
 * @author Zaahid Bateson
 */
class DatePart extends LiteralPart
{
    /**
     * @var DateTime the parsed date, or null if the date could not be parsed
     */
    protected $date;
    
    /**
     * Tries parsing the header's value as an RFC 2822 date, and failing that
     * into an RFC 822 date.
     * 
     * @param CharsetConverter $charsetConverter
     * @param string $token
     */
    public function __construct(CharsetConverter $charsetConverter, $token) {
        
        // parent::__construct converts character encoding -- may cause problems
        // sometimes.
        $dateToken = trim($token);
        parent::__construct($charsetConverter, $dateToken);
        
        $date = DateTime::createFromFormat(DateTime::RFC2822, $dateToken);
        if ($date === false) {
            $date = DateTime::createFromFormat(DateTime::RFC822, $dateToken);
        }
        $this->date = ($date === false) ? null : $date;
    }
    
    /**
     * Returns a DateTime object or false if it can't be parsed.
     * 
     * @return DateTime
     */
    public function getDateTime()
    {
        return $this->date;
    }
}
