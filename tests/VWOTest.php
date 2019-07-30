<?php
namespace vwo;
use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Exception;

/***
 * Class VWOTest
 * @package vwo
 */
final class VWOTest extends TestCase
{

    private $vwotest;
    private $settingsArr1='';
    private $settingsArr2='';
    private $settingsArr3='';
    private $settingsArr4='';
    private $settingsArr5='';
    private $settingsArr6='';
    private $variationResults='';





    /**
     *
     */
//    public function testTrackOnSuccess()
//    {
//        $this->vwotest = new VWO('60781','ea87170ad94079aa190bc7c9b85d26fb');
//        $result = $this->vwotest->trackGoal('FIRST','sshkshskjhs','REVENUE1');
//        $this->assertEquals(["status"=>"success"], $result);
//    }
//
//    public function testTrackOnFailure()
//    {
//        $this->vwotest = new VWO('60781','ea87170ad94079aa190bc7c9b85d26fb');
//        $result = $this->vwotest->trackGoal('FIRSTq','sshkshskjhs','REVENUE1');
//        $this->assertEquals(False, $result);
//    }

    private function getUsers() {
         $users = [
            'Ashley',
            'Bill',
            'Chris',
            'Dominic',
            'Emma',
            'Faizan',
            'Gimmy',
            'Harry',
            'Ian',
            'John',
            'King',
            'Lisa',
            'Mona',
            'Nina',
            'Olivia',
            'Pete',
            'Queen',
            'Robert',
            'Sarah',
            'Tierra',
            'Una',
            'Varun',
            'Will',
            'Xin',
            'You',
            'Zeba'
        ];

        return $users;
    }

    /***
     * @return mixed
     */
    private function getRandomUser() {
         $users = $this->getUsers();

        return $users[rand(0,25)];
    }

    protected function setUp()
    {
        $settings1= new Settings1();
        $settings2= new Settings2();
        $settings3= new Settings3();
        $settings4= new Settings4();
        $settings5= new Settings5();
        $settings6= new Settings6();
        $results= new VariationResults();

        $this->settingsArr1 = $settings1->setting;
        $this->settingsArr2 = $settings2->setting;
        $this->settingsArr3 = $settings3->setting;
        $this->settingsArr4 = $settings4->setting;
        $this->settingsArr5 = $settings5->setting;
        $this->settingsArr6 = $settings6->setting;
        $this->variationResults=$results->results;

    }

    public function testActivate()
    {
        for ($devtest=1;$devtest<7;$devtest++){
            $setting='settingsArr'.$devtest;
            $config=[
                'settings'=>$this->$setting,
                'isDevelopmentMode'=>1
            ];
            $this->vwotest = new VWO($config);
            $campaignName='DEV_TEST_'.$devtest;
            $users=$this->getUsers();
            for ($i=0;$i<26;$i++){
                try{
                    $userId=$users[$i];
                    $variationName=$this->vwotest->activate($campaignName,$userId);
                    $expected=ucfirst($this->variationResults[$campaignName][$userId]);
                    $this->assertEquals($expected, $variationName);
                }catch (Exception $e){

                }
            }
        }
    }
/*
    public function testGetVariation()
    {
        for ($devtest=1;$devtest<7;$devtest++){
            $setting='settingsArr'.$devtest;
            $config=[
                'settings'=>$this->$setting,
                'isDevelopmentMode'=>1
            ];
            $this->vwotest = new VWO($config);
            $campaignName='DEV_TEST_'.$devtest;
            $users=$this->getUsers();
            for ($i=0;$i<26;$i++){
                try{
                    $userId=$users[$i];
                    $variationName=$this->vwotest->getVariation($campaignName,$userId);
                    $expected=ucfirst($this->variationResults[$campaignName][$userId]);
                    $this->assertEquals($expected, $variationName);
                }catch (Exception $e){

                }
            }
        }
    }



    public function testTrack()
    {
        for ($devtest=1;$devtest<7;$devtest++){
            $setting='settingsArr'.$devtest;
            $config=[
                'settings'=>$this->$setting,
                'isDevelopmentMode'=>1
            ];
            $this->vwotest = new VWO($config);
            $campaignName='DEV_TEST_'.$devtest;
            $users=$this->getUsers();
            for ($i=0;$i<26;$i++){
                try{
                    $userId=$users[$i];
                    $goalname=$this->$setting['campaigns'][0]['goals'][0]['identifier'];
                    $result=$this->vwotest->track($campaignName,$userId,$goalname);
                    $expected=ucfirst($this->variationResults[$campaignName][$userId]);
                    if($expected == null){
                        $expected=false;
                    }else{
                        $expected=true;
                    }
                    $this->assertEquals($expected,$result);
                }catch (Exception $e){

                }
            }
        }
    }


*/
}
