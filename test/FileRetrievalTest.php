<?php
require_once DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'FileRetrieval.php';

class FileRetrievalTest extends PHPUnit_Framework_TestCase
{
    private function createExternalUrl($dirName){
        $dirPath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.$dirName;
        if(!is_dir($dirPath) ) {
            try{
                mkdir($dirPath);
                echo PHP_EOL.'Directory '.$dirPath. ' is created.';
            }catch(Exception $e){
                throw new Exception("Problem with creating drectory in ".__METHOD__.
                    ". Got message: ".$e->getMessage(), 1);
            }
        }else{
            throw new Exception("Error in: ".__METHOD__.
                    ". $dirPath exists!  Prefer not to touch exiting directory!", 1);
        }
        $fileName = 'test'.date("Y-d-M-H-i-s", time());
        $filePath = $dirPath.DIRECTORY_SEPARATOR.$fileName;
        $content = __METHOD__. date(" Y d M H:i:s ", time());
        if(file_put_contents($filePath, $content)){
            echo PHP_EOL.'File '.$filePath. ' is created.';
            return array('content' => $content, 'filename' => $fileName);
        };
    }

    private function removeExternalUrl($arr){
        if(!array_key_exists('dirname', $arr) || !array_key_exists('filename', $arr)){
            throw new Exception("Attention! Incorrect attempt to make use of code that deletes file. 
                Nothing is deleted.", 1);
        }
        $dirName = $arr['dirname'];
        $fileName = $arr['filename'];

        $dirPath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.$dirName;
        $filePath = $dirPath.DIRECTORY_SEPARATOR.$fileName;
        if(file_exists($filePath)){
            try{
                unlink($filePath);
                echo PHP_EOL.'File '.$filePath. ' is removed.';
            }catch(Exception $e){
                throw new Exception('File '.$filePath. ' is NOT removed: '.$e->getMessage(), 1);
            }
        }else{
            echo PHP_EOL.'Attention: there is no file '.$filePath.'! Are you sure you are using it correctly?'.PHP_EOL;
        }
        if(is_dir($dirPath)){
            try{
                rmdir($dirPath);
                echo PHP_EOL.'Directory '.$dirPath. ' is removed.'.PHP_EOL;
            }catch(Exception $e){
                throw new Exception('Directory '.$dirPath. ' is NOT removed: '.$e->getMessage(), 1);
            }
        }else{
            echo PHP_EOL.'Attention: no such directory! Are you sure you are using it correctly?'.PHP_EOL;
        }
    }

    public function testPresenceOfProperties(){
        $this->assertClassHasAttribute('url', 'FileRetrieval');
        $this->assertTrue(method_exists('FileRetrieval', 'retrieveFromWeb'));
    }

    public function testRetrieveFromWeb(){
        $startInfo = $this->createExternalUrl('webImitation3');
        $fileContent = $startInfo['content'];
        $fileName = $startInfo['filename']; 

        $retr = new FileRetrieval;
        $retr->setUrl("http://localhost/filtro/webImitation3/".$fileName);
        $page = $retr->retrieveFromWeb("http://localhost/filtro/webImitation3/".$fileName);
        $this->assertEquals($fileContent, $page);

        $retr2 = new FileRetrieval;
        $retr2->setUrl("http://localhost/filtro/webImitation3/".$fileName);
        $page2 = $retr2->retrieveFromWeb();
        $this->assertEquals($fileContent, $page2);
        $this->removeExternalUrl(array('dirname' => 'webImitation3', 'filename' => $fileName));
    }

    public function testRepoDirSetterGetter(){
        $this->assertTrue(method_exists('FileRetrieval', 'setRepoDir'));
        $f = new FileRetrieval;
        $f->setRepoDir('f:/a/b/c/d/');
        $this->assertEquals('f:/a/b/c/d/', $f->RepoDir());
    }


    public function testLocalPath(){
        $DS = DIRECTORY_SEPARATOR;
        $pp = new FileRetrieval;
        $pp->setUrl('http://www.portaportese.it/rubriche/Lavoro/test.page');
        $this->assertEquals('www.portaportese.it'.$DS.'rubriche'.$DS.'Lavoro'.$DS.'test.page', $pp->localPath());
        $this->assertEquals('http://www.portaportese.it/rubriche/Lavoro/test.page', $pp->url());

        $pp = new FileRetrieval;
        $pp->setUrl('http://www.portaportese.it/rubriche/Lavoro/');
        $this->assertEquals('www.portaportese.it'.$DS.'rubriche'.$DS.'Lavoro'.$DS.'default', $pp->localPath());
        $this->assertEquals('http://www.portaportese.it/rubriche/Lavoro/', $pp->url());

        $pp = new FileRetrieval;
        $pp->setUrl('http://www.portaportese.it/rubriche/Lavoro/page?a=1&b=2');
        $this->assertEquals('www.portaportese.it'.$DS.'rubriche'.$DS.'Lavoro'.$DS.'pageQa=1&b=2', $pp->localPath());
        $this->assertEquals('http://www.portaportese.it/rubriche/Lavoro/page?a=1&b=2', $pp->url());
    }
    /**
    * @group current
    */
 
    public function testGetFromRepo(){
        $baseDir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR;
        mkdir($baseDir.'repo');
        mkdir($baseDir.'repo'.DIRECTORY_SEPARATOR.'test1');
        mkdir($baseDir.'repo'.DIRECTORY_SEPARATOR.'test1'.DIRECTORY_SEPARATOR.'test2');
        $fileContent = __CLASS__.' '. date('d M Y H:i', time());
        $fileFullPath = $baseDir.'repo'.DIRECTORY_SEPARATOR.'test1'
            .DIRECTORY_SEPARATOR.'test2'.DIRECTORY_SEPARATOR.'default';
        $this->assertEquals(file_put_contents($fileFullPath, $fileContent), 
            strlen($fileContent));

        $fr = new FileRetrieval;
        $fr->setUrl('http://www.test.com/test1/test2/');
        $fr->setRepoDir(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'repo'.DIRECTORY_SEPARATOR);
        $content = $fr->retrieveFromRepo();
        $this->assertEquals($content, $fileContent);




    }
 
}
?>