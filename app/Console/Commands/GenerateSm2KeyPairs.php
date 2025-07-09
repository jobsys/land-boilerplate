<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rtgm\sm\RtSm2;

class GenerateSm2KeyPairs extends Command
{
	protected $signature = 'app:generate-sm2-key-pairs';

	protected $description = '生成SM2密钥对';

	public function handle(): void
	{
		$this->process();
	}

	public function process(): void
	{
		$sm2 = new RtSm2();
		[$privateKey, $publicKey] = $sm2->generatekey();

		// 更新 .env 文件
		land_write_env_file('SM2_PRIVATE_KEY', $privateKey);
		land_write_env_file('SM2_PUBLIC_KEY', $publicKey);

		$this->info('Private Key: ' . $privateKey);
		$this->info('Public Key: ' . $publicKey);

		$this->info('SM2 keys have been saved to .env');
	}

}
