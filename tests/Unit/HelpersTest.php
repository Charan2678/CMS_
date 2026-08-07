<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testHtmlEscapingHelper(): void
    {
        $input = '<script>alert("xss")</script>';
        $escaped = e($input);

        $this->assertEquals('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $escaped);
        $this->assertEquals('', e(null));
    }

    public function testCsrfTokenGenerationAndVerification(): void
    {
        $token = csrf_token();

        $this->assertNotEmpty($token);
        $this->assertTrue(csrf_verify($token));
        $this->assertFalse(csrf_verify('invalid_token'));
    }

    public function testUploadValidationHelper(): void
    {
        // 1. Error status upload
        $invalidErrorFile = ['error' => UPLOAD_ERR_NO_FILE];
        $res = validate_upload($invalidErrorFile);
        $this->assertFalse($res['ok']);

        // 2. Oversized upload
        $oversizedFile = [
            'error' => UPLOAD_ERR_OK,
            'size'  => 10 * 1024 * 1024, // 10MB
        ];
        $res = validate_upload($oversizedFile);
        $this->assertFalse($res['ok']);
        $this->assertStringContainsString('exceeds maximum allowed limit', $res['error']);

        // 3. Path traversal filename
        $traversalFile = [
            'name'     => '../../etc/passwd',
            'error'    => UPLOAD_ERR_OK,
            'size'     => 100,
            'tmp_name' => __FILE__,
        ];
        $res = validate_upload($traversalFile);
        $this->assertFalse($res['ok']);
        $this->assertStringContainsString('Path traversal detected', $res['error']);
    }
}
