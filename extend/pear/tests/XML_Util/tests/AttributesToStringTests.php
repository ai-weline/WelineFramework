<?php

class AttributesToStringTests extends AbstractUnitTests
{
    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringBasicUsage()
    {
        $original = ['foo' => 'bar','boo' => 'baz',];
        $expected = " boo=\"baz\" foo=\"bar\"";
        $this->assertEquals($expected, XML_Util::attributesToString($original));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithExplicitSortTrue()
    {
        $original = ['foo' => 'bar','boo' => 'baz',];
        $expected = " boo=\"baz\" foo=\"bar\"";
        $sort = true;
        $this->assertEquals($expected, XML_Util::attributesToString($original, $sort));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithExplicitSortFalse()
    {
        $original = ['foo' => 'bar','boo' => 'baz',];
        $expected = " foo=\"bar\" boo=\"baz\"";
        $sort = false;
        $this->assertEquals($expected, XML_Util::attributesToString($original, $sort));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithMultilineFalse()
    {
        $original = ['foo' => 'bar','boo' => 'baz',];
        $expected = " boo=\"baz\" foo=\"bar\"";
        $sort = true;
        $multiline = false;
        $this->assertEquals($expected, XML_Util::attributesToString($original, $sort, $multiline));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithMultilineTrue()
    {
        $original = ['foo' => 'bar','boo' => 'baz',];
        $expected =
<<< EOF
 boo="baz"
    foo="bar"
EOF;
        $sort = true;
        $multiline = true;
        $this->assertEquals($expected, XML_Util::attributesToString($original, $sort, $multiline));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithExplicitIndent()
    {
        $original = ['foo' => 'bar','boo' => 'baz',];
        $expected = " boo=\"baz\"\n        foo=\"bar\"";
        $sort = true;
        $multiline = true;
        $indent = '        '; // 8 spaces
        $this->assertEquals($expected, XML_Util::attributesToString($original, $sort, $multiline, $indent));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithExplicitLinebreak()
    {
        $original = ['foo' => 'bar','boo' => 'baz',];
        $expected = " boo=\"baz\"\n^foo=\"bar\"";
        $sort = true;
        $multiline = true;
        $linebreak = '^'; // some dummy character
        $this->assertEquals($expected, XML_Util::attributesToString($original, $sort, $multiline, $linebreak));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithOptionsThatIncludesSort()
    {
        $original = ['foo' => 'bar','boo' => 'baz',];
        $options = [
            'multiline' => true,
            'indent'    => '----',
            'linebreak' => "^",
            'entities'  => XML_UTIL_ENTITIES_XML,
            'sort'      => true,
        ];

        $expected = " boo=\"baz\"\n----foo=\"bar\"";
        $this->assertEquals($expected, XML_Util::attributesToString($original, $options));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithOptionsThatExcludesSort()
    {
        $original = ['foo' => 'bar','boo' => 'baz',];
        $options = [
            'multiline' => true,
            'indent'    => '----',
            'linebreak' => "^",
            'entities'  => XML_UTIL_ENTITIES_XML,
        ];

        $expected = " boo=\"baz\"\n----foo=\"bar\"";
        $this->assertEquals($expected, XML_Util::attributesToString($original, $options));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithEntitiesNone()
    {
        $original = ["foo" => "b@&r", "boo" => "b><z"];
        $expected = " boo=\"b><z\" foo=\"b@&r\"";
        $sort = true;
        $multiline = false;
        $linebreak = '    ';
        $this->assertEquals($expected, XML_Util::attributesToString($original, $sort, $multiline, $linebreak, PHP_EOL, XML_UTIL_ENTITIES_NONE));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithEntitiesXml()
    {
        $original = ["foo" => "b@&r", "boo" => "b><z"];
        $expected = " boo=\"b&gt;&lt;z\" foo=\"b@&amp;r\"";
        $sort = true;
        $multiline = false;
        $linebreak = '    ';
        $this->assertEquals($expected, XML_Util::attributesToString($original, $sort, $multiline, $linebreak, PHP_EOL, XML_UTIL_ENTITIES_XML));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithEntitiesXmlRequired()
    {
        $original = ["foo" => "b@&r", "boo" => "b><z"];
        $expected = " boo=\"b>&lt;z\" foo=\"b@&amp;r\"";
        $sort = true;
        $multiline = false;
        $linebreak = '    ';
        $this->assertEquals($expected, XML_Util::attributesToString($original, $sort, $multiline, $linebreak, PHP_EOL, XML_UTIL_ENTITIES_XML_REQUIRED));
    }

    /**
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithEntitiesHtml()
    {
        $original = ["foo" => "b@&r", "boo" => "b><z"];
        $expected = " boo=\"b&gt;&lt;z\" foo=\"b@&amp;r\"";
        $sort = true;
        $multiline = false;
        $linebreak = '    ';
        $this->assertEquals($expected, XML_Util::attributesToString($original, $sort, $multiline, $linebreak, PHP_EOL, XML_UTIL_ENTITIES_HTML));
    }

    /**
     * Tag attributes should not be treated as CDATA,
     * so the operation will instead quietly use XML_UTIL_ENTITIES_XML.
     *
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithCDataSectionForSingleAttribute()
    {
        $original = ['foo' => 'bar']; // need exactly one attribute here
        $options = [
            'sort'      => true,   // doesn't matter for this testcase
            'multiline' => false,  // doesn't matter for this testcase
            'indent'    => null,   // doesn't matter for this testcase
            'linebreak' => null,   // doesn't matter for this testcase
            'entities'  => XML_UTIL_CDATA_SECTION, // DOES matter for this testcase
        ];
        $expected = " foo=\"bar\"";
        $this->assertEquals($expected, XML_Util::attributesToString($original, $options));
    }

    /**
     * Tag attributes should not be treated as CDATA,
     * so the operation will instead quietly use XML_UTIL_ENTITIES_XML.
     *
     * @covers XML_Util::attributesToString()
     */
    public function testAttributesToStringWithCDataSectionForMultipleAttributesAndMultilineFalse()
    {
        $original = ['foo' => 'bar', 'boo' => 'baz']; // need more than one attribute here
        $options = [
            'sort'      => true,   // doesn't matter for this testcase
            'multiline' => false,  // DOES matter for this testcase, must be false
            'indent'    => null,   // doesn't matter for this testcase
            'linebreak' => null,   // doesn't matter for this testcase
            'entities'  => XML_UTIL_CDATA_SECTION, // DOES matter for this testcase
        ];
        $expected = " boo=\"baz\" foo=\"bar\"";
        $this->assertEquals($expected, XML_Util::attributesToString($original, $options));
    }
}
