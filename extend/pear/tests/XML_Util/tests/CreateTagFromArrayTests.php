<?php

class CreateTagFromArrayTests extends AbstractUnitTests
{
    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQname()
    {
        $original = [
            "qname" => "foo:bar",
        ];
        $expected = "<foo:bar />";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespace()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
        ];
        $expected = "<foo:bar xmlns:foo=\"http://foo.com\" />";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndAttributes()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
        ];
        $expected = "<foo:bar argh=\"fruit&amp;vegetable\" key=\"value\" xmlns:foo=\"http://foo.com\" />";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndAttributesAndContent()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $expected = "<foo:bar argh=\"fruit&amp;vegetable\" key=\"value\" xmlns:foo=\"http://foo.com\">I&apos;m inside the tag</foo:bar>";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndAttributesAndContent()
    {
        $original = [
            "qname" => "foo:bar",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $expected = "<foo:bar argh=\"fruit&amp;vegetable\" key=\"value\">I&apos;m inside the tag</foo:bar>";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndContent()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "content"      => "I'm inside the tag",
        ];
        $expected = "<foo:bar xmlns:foo=\"http://foo.com\">I&apos;m inside the tag</foo:bar>";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndAttributesAndContentWithEntitiesNone()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $expected = "<foo:bar argh=\"fruit&amp;vegetable\" key=\"value\" xmlns:foo=\"http://foo.com\">I'm inside the tag</foo:bar>";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original, XML_UTIL_ENTITIES_NONE));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndAttributesAndContentWithReplaceEntities()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $expected = "<foo:bar argh=\"fruit&amp;vegetable\" key=\"value\" xmlns:foo=\"http://foo.com\">I&apos;m inside the tag</foo:bar>";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original, XML_UTIL_REPLACE_ENTITIES));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndAttributesAndContentWithReplaceEntitiesAndMultilineFalse()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $multiline = false;
        $expected = "<foo:bar argh=\"fruit&amp;vegetable\" key=\"value\" xmlns:foo=\"http://foo.com\">I&apos;m inside the tag</foo:bar>";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original, XML_UTIL_REPLACE_ENTITIES, $multiline));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndAttributesAndContentWithReplaceEntitiesAndMultilineTrue()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $multiline = true;
        $expected =
<<< EOF
<foo:bar argh="fruit&amp;vegetable"
         key="value"
         xmlns:foo="http://foo.com">I&apos;m inside the tag</foo:bar>
EOF;
        $this->assertEquals($expected, XML_Util::createTagFromArray($original, XML_UTIL_REPLACE_ENTITIES, $multiline));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndAttributesAndContentWithReplaceEntitiesAndMultilineTrueAndIndent()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $multiline = true;
        $indent = "  ";
        $expected =
<<< EOF
<foo:bar argh="fruit&amp;vegetable"
  key="value"
  xmlns:foo="http://foo.com">I&apos;m inside the tag</foo:bar>
EOF;
        $this->assertEquals($expected, XML_Util::createTagFromArray($original, XML_UTIL_REPLACE_ENTITIES, $multiline, $indent));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndAttributesAndContentWithReplaceEntitiesAndMultilineTrueAndIndentAndLinebreak()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $multiline = true;
        $indent = "  ";
        $linebreak = "^";
        $expected = "<foo:bar argh=\"fruit&amp;vegetable\"^  key=\"value\"^  xmlns:foo=\"http://foo.com\">I&apos;m inside the tag</foo:bar>";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original, XML_UTIL_REPLACE_ENTITIES, $multiline, $indent, $linebreak));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndAttributesAndContentWithReplaceEntitiesAndMultilineTrueAndIndentAndLinebreakAndSortAttributesTrue()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $multiline = true;
        $indent = "  ";
        $linebreak = "^";
        $sortAttributes = true;
        $expected = "<foo:bar argh=\"fruit&amp;vegetable\"^  key=\"value\"^  xmlns:foo=\"http://foo.com\">I&apos;m inside the tag</foo:bar>";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original, XML_UTIL_REPLACE_ENTITIES, $multiline, $indent, $linebreak, $sortAttributes));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameAndNamespaceAndAttributesAndContentWithReplaceEntitiesAndMultilineTrueAndIndentAndLinebreakAndSortAttributesFalse()
    {
        $original = [
            "qname" => "foo:bar",
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $multiline = true;
        $indent = "  ";
        $linebreak = "^";
        $sortAttributes = false;
        $expected = "<foo:bar key=\"value\"^  argh=\"fruit&amp;vegetable\"^  xmlns:foo=\"http://foo.com\">I&apos;m inside the tag</foo:bar>";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original, XML_UTIL_REPLACE_ENTITIES, $multiline, $indent, $linebreak, $sortAttributes));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithInvalidArray()
    {
        $badArray = [
            "foo" => "bar",
        ];
        $expectedError = "You must either supply a qualified name (qname) or local tag name (localPart).";
        $this->assertEquals($expectedError, XML_Util::createTagFromArray($badArray));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithNamespaceAndAttributesAndContentButWithoutQname()
    {
        $original = [
            "namespaceUri" => "http://foo.com",
            "attributes"   => [ "key" => "value", "argh" => "fruit&vegetable" ],
            "content"      => "I'm inside the tag",
        ];
        $expectedError = "You must either supply a qualified name (qname) or local tag name (localPart).";
        $this->assertEquals($expectedError, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithNonScalarContent()
    {
        $badArray = [
            'content' => ['foo', 'bar'],
        ];
        $expectedError = "Supplied non-scalar value as tag content";
        $this->assertEquals($expectedError, XML_Util::createTagFromArray($badArray));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithArrayOfNamespaces()
    {
        $original = [
            'qname'        => 'foo:bar',
            'namespaces'   => ['ns1' => 'uri1', 'ns2' => 'uri2'],
        ];
        $expected = "<foo:bar xmlns:ns1=\"uri1\" xmlns:ns2=\"uri2\" />";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameDerivedFromNamespaceUriAndLocalPart()
    {
        $original = [
            'namespaceUri' => 'http://bar.org',
            'localPart'    => 'foo'
        ];
        $expected = "<foo xmlns=\"http://bar.org\" />";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameDerivedFromNamespaceAndLocalPart()
    {
        $original = [
            'namespace'    => 'http://foo.org',
            'localPart'    => 'bar'
        ];
        $expected = "<http://foo.org:bar />";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithQnameDerivedFromLocalPart()
    {
        $original = [
            'namespace'    => '',
            'localPart'    => 'bar'
        ];
        $expected = "<bar />";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original));
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayWithImplicitlyEmptyContentAndCollapseNoneDoesNotCollapseTag()
    {
        $original = ['qname' => 'tag1'];
        $expected = "<tag1></tag1>";
        $actual = XML_Util::createTagFromArray(
            $original,
            XML_UTIL_REPLACE_ENTITIES,  // default $replaceEntities
            false,                      // default $multiline
            '_auto',                    // default $indent
            "\n",                       // default $linebreak
            true,                       // default $sortAttributes
            XML_UTIL_COLLAPSE_NONE
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers XML_Util::createTagFromArray()
     */
    public function testCreateTagFromArrayForCdataWithExplicitlyEmptyContentDoesNotCollapseTag()
    {
        $original = ['qname' => 'tag1', 'content' => ''];
        $expected = "<tag1><![CDATA[]]></tag1>";
        $this->assertEquals($expected, XML_Util::createTagFromArray($original, XML_UTIL_CDATA_SECTION));
    }
}
