<?php
namespace FakeXml;

require_once "autoload.php";


class Generator
{
    
    /**
     * Root Tag
     * @var string 
     */
    protected $root     = 'gantt';
    
    /**
     * The layers available
     * @var array 
     */
    protected $layers = [
        [
            'name'       => 'Active Layer',
            'colour'     => 'Active Layer',
            'textcolour' => 'ffffff',
        ],
        [
            'name'       => 'Active Layer',
            'colour'     => 'Active Layer',
            'textcolour' => 'ffffff',
        ],
    ];
    

    /**
     * Class instance
     * @var Generator 
     */
    protected static $instance  = null;

    /**
     * Creates new XML data that can be used in OCA Tree
     * @param boolean $sendHeaders If true, we are to send 'application/xml' content type
     * @return string The well-formed XML text
     */
    public static function create($sendHeaders = false)
    {
        // We'll be using a stripped down version of singleton
        if (null === self::$instance) {
            self::$instance = new self();
        }
        // Generate the tags
        $layers     = self::$instance->getLayersTag();
        echo "\n{$layers}\n";
    }
    
    /**
     * This creates XML tags with the specified attributes
     * @param string $tagName      The tag name
     * @param string $innerContent The inner XML content. Only used for non-single tags!!
     * @param array $attr          The array attribute
     * @param boolean $singleTag   If true a single tag is created
     * @return string The well formed tag
     */
    protected function tag($tagName, $innerContent = '', array $attr = array(), $singleTag = false)
    {
        $attrList   = '"' . implode('", "', $attr) . '"';
        return "<$tagName $attrList" . ($singleTag ? ' />' : "$innerContent</$tagName>");
    }
    
    /**
     * Returns the list of layers
     * @return string
     */
    protected function getLayersTag()
    {
        $tagString  = '';
        foreach ($this->layers as $layer) {
            $tagString .= $this->tag('layer', '', $layer, true) . PHP_EOL;
        }
        return $this->tag($tagString, $tagString);
    }
    
    /**
     * Returns the list of items
     * @return string
     */
    protected function getItems()
    {
        
    }
    
    /**
     * Generates an item
     * @param type $itemId
     * @return string Item tag with random content
     */
    protected function makeItem($itemId)
    {
        
    }
    
    /**
     * Generates a node
     * @param string $itemId
     * @param string $itemName
     * @param string $startDate
     * @param string $endDate
     * @param string $oparg
     * @return string Node tag with random content
     */
    protected function makeNode($itemId, $itemName, $startDate, $endDate, $oparg = '')
    {
        
    }
    
    
}


\FakeXml\Generator::create();