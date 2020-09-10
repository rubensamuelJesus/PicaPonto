<?php

use Illuminate\Database\Seeder;

class AccessSeeder extends Seeder
{
	/*static function access_id() 
	{
		return [
			/*['21st-Century-Architecture.jpg', 1, '21st Century Architecture ', false],
			['A-Home-in-Heels.jpg', 1, 'A Home in Heels ', false],
			['Adrift-on-a-Stollen-Moon.jpg', 1, 'Adrift on a Stollen Moon ', true],
			['Appetite-for-Precision.jpg', 1, 'Appetite for Precision ', false],
			
		];
	}*/

    //private $coverPath = 'public/covers';
    private $faker = null;
    private $contadorGlobal = 0;

    public function run()
    {
        $this->command->line("");
        $this->command->line("##################################################################################");
        $this->command->line("Creating Access");
        $this->command->line("##################################################################################");

        //Storage::deleteDirectory($this->coverPath);
        //Storage::makeDirectory($this->coverPath);
        $this->faker = Faker\Factory::create('pt_PT');       	
        for ($i=0; $i < 8; $i++) {
        	$this->addAccess($this->faker,$i);
        }
    }

    /*private function copyCoverPhoto($filename)
    {
        $targetDir = storage_path('app/'.$this->coverPath);

        $newFileName = str_random(16) . ".jpg";

        File::copy(database_path('seeds/cover_photos')."/$filename", $targetDir . '/' . $newFileName);
        return $newFileName;
    }*/

    private function addAccess(Faker\Generator $faker,$i)
    {
        //$newFileName = $this->copyCoverPhoto($bookInfo[0]);
        //$createdAt = Carbon\Carbon::now()->subDays(600);

        $access = [
        	'user_id'=>$this->getRandomUser(),
        	'rfid_id'=>'A1 B2 C3 D'.$i,
           /* 'title' => $bookInfo[2],
            'description' => $faker->realText(200),
            'cover_url' => $newFileName,
            'total_copies' => $bookInfo[1],
            'copies_on_library' => $bookInfo[1],
            'created_at' => $createdAt,
            'updated_at' => $faker->dateTimeBetween($createdAt),
            'deleted_at' => $bookInfo[3] ? $this->faker->dateTimeBetween($createdAt) : null,*/
        ];
        $this->contadorGlobal ++;
        DB::table('access_id')->insert($access);
        $this->command->info("Created Access {$this->contadorGlobal}: " . $access['user_id']);
    }


    private function getRandomUser()
    {
        static $users;
        $users = $users != null ? $users : DB::table('users')->pluck('id');
        return $users->random();
    }

}
