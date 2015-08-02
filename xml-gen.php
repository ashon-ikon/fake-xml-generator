<?php

namespace FakeXml;

// Setting the timezone
date_default_timezone_set('Australia/Sydney');

require_once "vendor/autoload.php";

use Faker\Factory;

class Generator {

    /**
     * Root Tag
     * @var string 
     */
    protected $root = 'gantt';

    /**
     * The layers available
     * @var array 
     */
    protected $layers = [
        [
            'name'       => 'Active Layer',
            'colour'     => '00a388',
            'textcolour' => 'ffffff',
        ],
        [
            'name'       => 'Inactive Layer',
            'colour'     => 'ccede7',
            'textcolour' => 'ffffff',
        ],
    ];

    /**
     *
     * @var Factory 
     */
    protected $faker = null;

    const MAX_CHILDREN_ALLOWED = 20;

    /**
     * Class instance
     * @var Generator 
     */
    protected static $instance = null;

    protected function __construct() {
        // use the factory to create a Faker\Generator instance
        $this->faker = Factory::create();
    }

    /**
     * Creates new XML data that can be used in OCA Tree
     * @param boolean $sendHeaders If true, we are to send 'application/xml' content type
     * @return string The well-formed XML text
     */
    public static function create($sendHeaders = false) {
        // We'll be using a stripped down version of singleton
        if (null === self::$instance) {
            self::$instance = new self();
        }
        // Generate the tags
        $layers = self::$instance->getLayersTag();
        $items  = self::$instance->getItemsTag();


        if ($sendHeaders) {
            header('Content-type: text/xml; charset=utf-8');
        }
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . PHP_EOL;
        $xml .= self::$instance->tag(self::$instance->root, "\n\t{$layers}\n\t{$items}\n");
        return $xml;
    }

    /**
     * This creates XML tags with the specified attributes
     * @param string $tagName      The tag name
     * @param string $innerContent The inner XML content. Only used for non-single tags!!
     * @param array $attr          The array attribute
     * @param boolean $singleTag   If true a single tag is created
     * @return string The well formed tag
     */
    protected function tag($tagName, $innerContent = '', array $attr = array(), $singleTag = false) {
        $attribtue = '';
        foreach ($attr as $key => $value) {
            $attribtue .= " {$key}='{$value}'";
        }
        if ($attribtue) {
            $attribtue = trim($attribtue);
        }
        return "<$tagName $attribtue" . ($singleTag ? ' />' : ">$innerContent</$tagName>");
    }

    /**
     * Returns the list of layers
     * @return string
     */
    protected function getLayersTag() {
        $tagString = '';
        foreach ($this->layers as $layer) {
            $tagString .= $this->tag('layer', '', $layer, true) . PHP_EOL;
        }
        return $this->tag('layers', "\n{$tagString}\n");
    }

    /**
     * Returns the list of items
     * @return string
     */
    protected function getItemsTag() {
        $itemsStr = '';


        // Create the child items
        $groups = [
            '100' => 'Primary Team Member',
            '101' => 'Secondary Team Member',
            '102' => 'Trainee'
        ];
        foreach ($groups as $rootId => $groupLabel) {
            $subChildrenCount = $this->faker->numberBetween(0, 25);
            $childrenStr      = '';
            foreach ($subChildrenCount ? range(1, $subChildrenCount) : [] as $i) {
                $childrenStr .= "\n\t" . $this->makeItem("{$rootId}__{$i}", 1);
            }
            $itemsStr .= "\n\t" . $this->makeItem(
                            $rootId, $subChildrenCount, !!$subChildrenCount, $childrenStr, '', $groupLabel
            );
        }

        // Creaate the root item
        return $this->tag('items', "\n\t{$itemsStr}\n");
    }

    /**
     * Generates an item
     * @param integer $itemId
     * @param integer $nodeCount
     * @param boolean $hasChildren
     * @param string $content
     * @param string $oparg
     * @param string $label
     * @return string Item tag with random content
     */
    protected function makeItem($itemId, $nodeCount = 3, $hasChildren = false, $content = '', $oparg = '', $label = '') {
        $nodesList = '';
        if ($nodeCount) {
            foreach (range(0, $nodeCount) as $surfix) {
                $start     = rand(-6, 5);
                $startDate = strtotime("{$start} Weeks ");
                $endDate   = date('Y-m-d H:i:s', $startDate + (60 * 60 * 24 * $this->faker->numberBetween(2, 20)));
                $nodesList .= "\t\t" . $this->makeNode(
                    "{$itemId}-{$surfix}", date('Y-m-d H:i:s', $startDate), $endDate, $oparg
                );
            }
        }

        $dataAttr = [
            'value' => ($hasChildren ? 'Has' : 'No') . ' Children',
            'name'  => ucfirst($this->faker->word),
        ];
        $dataTag  = $this->tag('data', '', $dataAttr, true);

        $itemAttr = [
            'pk'    => $itemId,
            'label' => $label ? $label : \htmlentities($this->faker->name),
            'oparg' => $oparg,
        ];

        return $this->tag('item', "\n\t{$dataTag}\n\t{$nodesList}\n\t{$content}\n", $itemAttr);
    }

    /**
     * Generates a node
     * @param string $nodeId
     * @param string $startDate
     * @param string $endDate
     * @param string $oparg
     * @return string Node tag with random content
     */
    protected function makeNode($nodeId, $startDate, $endDate, $oparg = '') {
        $detail     = $this->tag('detail', \htmlentities($this->faker->text(150)));
        $start      = strtotime($startDate);
        $end        = strtotime($endDate);
//        $middleDate = date('Y-m-d H:i:s', $this->faker->numberBetween($start, $end));
        $attr       = [
            'pk'    => $nodeId,
            'label' => 'Primary',
            'start' => $startDate,
            'end'   => $endDate,
            'layer' => $this->layers[0]['name'],
            'oparg' => $oparg,
        ];
        $first      = $this->tag('node', $detail, $attr);
//        // use the second layer
//        $attrTwo    = array_merge($attr, [
//            'pk'    => "{$nodeId}-s",
//            'label' => '&nbsp;', // We'll empty this
//            'start' => $middleDate,
//            'end'   => $endDate,
//            'layer' => $this->layers[1]['name'],
//        ]);
//        $second     = $this->tag('node', $this->tag('detail', ''), $attrTwo);
        return "\n\t\t{$first}";
    }

}

echo Generator::create();
