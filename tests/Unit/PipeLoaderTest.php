<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Loaders\PipeLoader;
use Tests\TestCase;

/**
 * Class PipeLoaderTest
 * @package Tests\Unit
 */
class PipeLoaderTest extends TestCase {

    use RefreshDatabase;

    public function test_it_correctly_identifies_process_type_format() {
        $valid1 = 'relationType|5e38cb68-0970-470d-b41e-752b30b88c2b';
        $valid2 = 'relationType|5e38cb68-0970-470d-b41e-752b30b88c2b[RT 1]';
        $valid3 = 'allisa/demo::relationType|5e38cb68-0970-470d-b41e-752b30b88c2b[RT 1]';
        $valid4 = 'allisa/demo@1.0.0::relationType|5e38cb68-0970-470d-b41e-752b30b88c2b[RT 1]';
        $valid5 = 'allisa/demo@1.0.0::relationType|test';
        $valid6 = 'allisa/demo@1.0.0::relationType|test[RT -1]';

        $this->assertTrue(PipeLoader::hasValidAbtractModelFormat($valid1));
        $this->assertTrue(PipeLoader::hasValidAbtractModelFormat($valid2));
        $this->assertTrue(PipeLoader::hasValidAbtractModelFormat($valid3));
        $this->assertTrue(PipeLoader::hasValidAbtractModelFormat($valid4));
        $this->assertTrue(PipeLoader::hasValidAbtractModelFormat($valid5));
        $this->assertTrue(PipeLoader::hasValidAbtractModelFormat($valid6));

        $invalid1 = 'process|5e38cb68-0970-470d-b41e-752b30b88c2b';
        $invalid2 = 'process|';
        $invalid3 = 'process';
        $invalid4 = 'allisa/demo::process';
        $invalid5 = 'allisa/demo@1.0.0::process';
        $invalid6 = 'relationType|';
        $invalid7 = 'relationType|[test]';
        $invalid8 = 'relationType|[test]';

        $this->assertFalse(PipeLoader::hasValidAbtractModelFormat($invalid1));
        $this->assertFalse(PipeLoader::hasValidAbtractModelFormat($invalid2));
        $this->assertFalse(PipeLoader::hasValidAbtractModelFormat($invalid3));
        $this->assertFalse(PipeLoader::hasValidAbtractModelFormat($invalid4));
        $this->assertFalse(PipeLoader::hasValidAbtractModelFormat($invalid5));
        $this->assertFalse(PipeLoader::hasValidAbtractModelFormat($invalid6));
        $this->assertFalse(PipeLoader::hasValidAbtractModelFormat($invalid7));
        $this->assertFalse(PipeLoader::hasValidAbtractModelFormat($invalid8));

    }
}


