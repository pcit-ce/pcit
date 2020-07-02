<?php

declare(strict_types=1);

namespace PCIT\Plugin\Toolkit\Tests;

use PCIT\Plugin\Toolkit\Core;
use Tests\TestCase;

class CoreTest extends TestCase
{
    /**
     * @var Core
     */
    public $core;

    public function setUp(): void
    {
        $this->core = new Core();
    }

    public function test_debug(): void
    {
        $this->core->debug('debug % '."\r".' '."\n".' : ,');
        $this->expectOutputString('::debug::debug %25 %0D %0A : ,'."\n");
    }

    public function test_info(): void
    {
        $this->core->info('info % '."\r".' '."\n".' : ,');
        $this->expectOutputString('info % '."\r".' '."\n".' : ,'."\n");
    }

    public function test_warning(): void
    {
        $this->core->warning('warning % '."\r".' '."\n".' : ,');
        $this->expectOutputString('::warning::warning %25 %0D %0A : ,'."\n");
    }

    public function test_warning_1(): void
    {
        $this->core->warning('warning', 'file', 1, 2);
        $this->expectOutputString('::warning file=file,line=1,col=2::warning'."\n");
    }

    public function test_warning_2(): void
    {
        $this->core->warning('warning', null, 1, 2);
        $this->expectOutputString('::warning::warning'."\n");
    }

    public function test_warning_3(): void
    {
        $this->core->warning('warning', 'file', null, 2);
        $this->expectOutputString('::warning file=file::warning'."\n");
    }

    public function test_warning_4(): void
    {
        $this->core->warning('warning', 'file', 1, null);
        $this->expectOutputString('::warning file=file,line=1::warning'."\n");
    }

    public function test_error(): void
    {
        $this->core->error('error % '."\r".' '."\n".' : ,');
        $this->expectOutputString('::error::error %25 %0D %0A : ,'."\n");
    }

    public function test_startGroup(): void
    {
        $this->core->startGroup('group % '."\r".' '."\n".' : ,');
        $this->expectOutputString('::group::group %25 %0D %0A : ,'."\n");
    }

    public function test_endGroup(): void
    {
        $this->core->endGroup();
        $this->expectOutputString('::endgroup::'."\n");
    }

    public function test_exportVariable(): void
    {
        $this->core->exportVariable('var', 'value % '."\r".' '."\n".' : ,');
        $this->expectOutputString('::set-env name=var::value %25 %0D %0A : ,'."\n");
    }

    public function test_setOutput(): void
    {
        $this->core->setOutput('output', 'value % '."\r".' '."\n".' : ,');
        $this->expectOutputString('::set-output name=output::value %25 %0D %0A : ,'."\n");
    }

    public function test_getInput(): void
    {
        $this->core->exportVariable('INPUT_VAR', 'value % '."\r".' '."\n".' : ,');
        $this->expectOutputString('::set-env name=INPUT_VAR::value %25 %0D %0A : ,'."\n");
        $result = $this->core->getInput('var');
        $this->assertEquals('value % '."\r".' '."\n".' : ,', $result);
    }

    public function test_saveState(): void
    {
        $this->core->saveState('state', 'value % '."\r".' '."\n".' : ,');
        $this->expectOutputString('::save-state name=state::value %25 %0D %0A : ,'."\n");
    }

    public function test_getState(): void
    {
        $this->core->exportVariable('STATE_STATE', 'value % '."\r".' '."\n".' : ,');
        $this->expectOutputString('::set-env name=STATE_STATE::value %25 %0D %0A : ,'."\n");
        $result = $this->core->getState('state');
        $this->assertEquals('value % '."\r".' '."\n".' : ,', $result);
    }

    public function test_isDebug(): void
    {
        $this->assertFalse($this->core->isDebug());

        $this->core->exportVariable('PCIT_STEP_DEBUG', 'true');
        $this->expectOutputString('::set-env name=PCIT_STEP_DEBUG::true'."\n");
        $this->assertTrue($this->core->isDebug());
    }

    // public function test_setFailed(){
    //     $this->core->setFailed('failed');
    //     $this->expectOutputString('::error::failed'."\n");
    // }

    public function test_setSecret(): void
    {
        $this->core->setSecret('secret % '."\r".' '."\n".' : ,');
        $this->expectOutputString('::add-mask::secret %25 %0D %0A : ,'."\n");
    }
}
