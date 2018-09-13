<?php

namespace Tests\Unit\Ssl;

use App\Support\Ssl\CertificateBuilder;
use Tests\BaseTestCase;

class CertificateBuilderBaseTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_creates_a_certificate()
    {
        $dir = storage_path('test_library/ssl');

        $builder = new CertificateBuilder($dir);
        $builder->build('klever.test');

        $this->assertFileExists($dir.'/klever.test.conf');
        $this->assertFileExists($dir.'/klever.test.crt');
        $this->assertFileExists($dir.'/klever.test.csr');
        $this->assertFileExists($dir.'/klever.test.key');

        $this->assertFileExists($dir.'/KleverPorterSelfSigned.key');
        $this->assertFileExists($dir.'/KleverPorterSelfSigned.pem');
        $this->assertFileExists($dir.'/KleverPorterSelfSigned.srl');
    }

    /** @test */
    public function it_removes_a_certificate()
    {
        $dir = storage_path('test_library/ssl');

        @touch($dir.'/klever.test.conf');
        @touch($dir.'/klever.test.crt');
        @touch($dir.'/klever.test.csr');
        @touch($dir.'/klever.test.key');

        $builder = new CertificateBuilder($dir);
        $builder->destroy('klever.test');

        $this->assertFileNotExists($dir.'/klever.test.conf');
        $this->assertFileNotExists($dir.'/klever.test.crt');
        $this->assertFileNotExists($dir.'/klever.test.csr');
        $this->assertFileNotExists($dir.'/klever.test.key');
    }
}
