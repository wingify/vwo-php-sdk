<?php
namespace vwo;
use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Exception;
use vwo\Utils\UserProfileInterface;
use vwo\Logger\LoggerInterface;

/**
 * Class CustomLogger
 */
Class CustomLogger implements LoggerInterface{

    /**
     * @param $message
     * @param $level
     * @return string
     */
    public function addLog($message,$level){
        //do code for writing logs to your files/databases
        //throw new Exception('my test');
        //return $x;

    }

}

Class UserProfileTest implements UserProfileInterface{

    /**
     * @param $userId
     * @param $campaignName
     * @return string
     */
    public function lookup($userId,$campaignName){
        // xyz actions
        return[
            'userId'=>$userId,
            $campaignName=>['variationName'=>'Control']
        ];

    }

    /**
     * @param $campaignInfo
     * @return bool
     */
    public function save($campaignInfo){
        // print_r($campaignInfo);
        return True;

    }

}

/***
 * Class VWOTest
 * @package vwo
 */
class VWOTest extends TestCase
{

    private $vwotest;
    private $settingsArr1='';
    private $settingsArr2='';
    private $settingsArr3='';
    private $settingsArr4='';
    private $settingsArr5='';
    private $settingsArr6='';
    private $variationResults='';


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

    public function testGetSettings(){
        $accountId='12345';
        $sdkKey='1111111111111111111111';
        $result=VWO::getSettingsFile($accountId,$sdkKey);
        $expected=False;
        $obj=new VWO('');
        $config=[
            'settingsFile'=>$this->settingsArr1,
            'isDevelopmentMode'=>1
        ];
        $obj=new VWO($config);
        $obj->activate('LOREM','Ian');
        $this->assertEquals($expected, $result);
    }


    public function testActivate()
    {
        for ($devtest=1;$devtest<7;$devtest++){
            $setting='settingsArr'.$devtest;
            $config=[
                'settingsFile'=>$this->$setting,
                'isDevelopmentMode'=>0
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
    public function testGetVariation()
    {
        for ($devtest=1;$devtest<7;$devtest++){
            $setting='settingsArr'.$devtest;
            $config=[
                'settingsFile'=>$this->$setting,
                'isDevelopmentMode'=>0
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
                'settingsFile'=>$this->$setting,
                'isDevelopmentMode'=>0
            ];
            $this->vwotest = new VWO($config);
            $campaignName='DEV_TEST_'.$devtest;
            $users=$this->getUsers();
            for ($i=0;$i<26;$i++){
                try{
                    $userId=$users[$i];
                    $goalname=$config['settingsFile']['campaigns'][0]['goals'][0]['identifier'];
                    $result=$this->vwotest->track($campaignName,$userId,$goalname,'testRevenue');
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

    public function testTrackForUser()
    {
        $setting='settingsArr1';
        $config=[
            'settingsFile'=>$this->$setting,
            'isDevelopmentMode'=>0,
            'logging'=>new CustomLogger(),
            'userProfileService'=> new userProfileTest()
        ];
        $this->vwotest = new VWO($config);
        $campaignName='DEV_TEST_1';
        $users=$this->getUsers();
        $userId=$users[0];
        $goalname=$config['settingsFile']['campaigns'][0]['goals'][0]['identifier'];
        $result=$this->vwotest->track($campaignName,$userId,$goalname);
        $expected=ucfirst($this->variationResults[$campaignName][$userId]);
        if($expected == null){
            $expected=false;
        }else{
            $expected=true;
        }
        $this->assertEquals($expected,$result);
    }
}
