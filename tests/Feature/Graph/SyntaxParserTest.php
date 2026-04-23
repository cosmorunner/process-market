<?php

namespace Tests\Feature\Graph;

use App\Graph\SyntaxParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SyntaxParserTest
 * @package Tests\Feature\Graph
 */
class SyntaxParserTest extends TestCase {

    use RefreshDatabase;

    public function test_graph_syntax_parser_can_action_output_name() {
        // removes title ((...)) and filters the name out of [[action.outputs.name]]
        $syntax = '[[action.outputs.testvalue((Aktions-Daten - testvalue))]]';
        $newSyntax = SyntaxParser::actionOutputName($syntax);
        $this->assertEquals('testvalue', $newSyntax);

        // removes title ((...)) but does not filter the name, since its process.outputs and not action.outputs
        $syntax = '[[process.outputs.testvalue((Prozess-Daten - testvalue))]]';
        $newSyntax = SyntaxParser::actionOutputName($syntax);
        $this->assertEquals('[[process.outputs.testvalue]]', $newSyntax);

        // does not filter anything, since the title misses a ")" -> ((...)
        $syntax = '[[action.outputs.testvalue((Prozess-Daten - testvalue)]]';
        $newSyntax = SyntaxParser::actionOutputName($syntax);
        $this->assertEquals('[[action.outputs.testvalue((Prozess-Daten - testvalue)]]', $newSyntax);

        // does not filter the name, since it misses a "]" -> [[...]
        $syntax = '[[action.outputs.testvalue((Prozess-Daten - testvalue))]';
        $newSyntax = SyntaxParser::actionOutputName($syntax);
        $this->assertEquals('[[action.outputs.testvalue]', $newSyntax);

        // does not filter the name, since it contains a capital letter
        $syntax = '[[action.outputs.testValue((Prozess-Daten - testValue))]]';
        $newSyntax = SyntaxParser::actionOutputName($syntax);
        $this->assertEquals('[[action.outputs.testValue]]', $newSyntax);
    }
}
