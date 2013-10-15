<?php
define('DS', DIRECTORY_SEPARATOR);
require_once DS.'..'.DS.'core'.DS.'FileRetrieval.php';

class FileRetrievalTest extends PHPUnit_Framework_TestCase
{

    /**
    * Creates a folder named $dirName in the root folder, which is accessible by 
    * http://localhost/filtro/webImitation3/filename
    * where 'filename' is one of the keys of the return value of this function.
    * Another key is 'content' - the content of the newly created file.
    * @return array() array('filename' => ..., 'content' => ...)
    */
    private function createExternalUrl($dirName){
        $dirPath = dirname(dirname(__FILE__)).DS.$dirName;
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
        $filePath = $dirPath.DS.$fileName;
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

        $dirPath = dirname(dirname(__FILE__)).DS.$dirName;
        $filePath = $dirPath.DS.$fileName;
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

    /**
    * 1. In root folder, creates the repository named $repoName.
    * 2. In the above folder creates nested other folder: www.test.it/test1/
    * 3. In the above folder creates a file "foo.bar" 
    * @return string the content of the file $repoName/www.test.it/test1/foo.bar
    */
    private function createRepo($repoName){
        $baseDir = dirname(dirname(__FILE__)).DS;
        mkdir($baseDir.$repoName);
        mkdir($baseDir.$repoName.DS.'www.test.com');
        mkdir($baseDir.$repoName.DS.'www.test.com'.DS.'test1');
        $fileContent = __METHOD__.' '. date('d M Y H:i', time());
        $fileFullPath = $baseDir.$repoName.DS.'www.test.com'
            .DS.'test1'.DS.'foo.bar';
        $this->assertEquals(file_put_contents($fileFullPath, $fileContent), 
            strlen($fileContent));
        return $fileContent;
    }

    /**
    * Removes the folder $repoName containing this file: www.test.it/test1/foo.bar
    */
    private function removeRepo($repoName){
        $baseDir = dirname(dirname(__FILE__)).DS;
        $fileFullPath = $baseDir.$repoName.DS.'www.test.com'
            .DS.'test1'.DS.'foo.bar';
        unlink($fileFullPath);
        rmdir($baseDir.$repoName.DS.'www.test.com'.DS.'test1');
        rmdir($baseDir.$repoName.DS.'www.test.com');
        rmdir($baseDir.$repoName);
        
 
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
        $DS = DS;
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



    public function testRetrieveFromRepo(){
        $fileContent = $this->createRepo('repo3');
  
        $fr = new FileRetrieval;
        $fr->setUrl('http://www.test.com/test1/foo.bar');
        $fr->setRepoDir(dirname(dirname(__FILE__)).DS.'repo3'.DS);
        $content = $fr->retrieveFromRepo();
        $this->assertEquals($content, $fileContent);


        $fr = new FileRetrieval;
        $fr->setUrl('http://www.test.com/test1/doesnotexist');
        $fr->setRepoDir(dirname(dirname(__FILE__)).DS.'repo3'.DS);
        $this->assertFalse($fr->retrieveFromRepo());

        $this->removeRepo('repo3');

    }



    public function testLocalCopyExists(){
        $this->assertTrue(method_exists('FileRetrieval', 'localCopyExists'));

        $this->createRepo('repo2');

        $fr = new FileRetrieval;
        $fr->setUrl('http://www.test.com/test1/foo.bar');
        $fr->setRepoDir(dirname(dirname(__FILE__)).DS.'repo2'.DS);
        $this->assertTrue($fr->localCopyExists());

        $fr = new FileRetrieval;
        $fr->setUrl('http://www.test.com/test1/doesnotexist.html');
        $fr->setRepoDir(dirname(dirname(__FILE__)).DS.'repo2'.DS);
        $this->assertFalse($fr->localCopyExists());

        $this->removeRepo('repo2');
    }
    
    /**
    * @group current
    */

    public function testSaveInRepo(){
        $fr = $this->getmock('FileRetrieval', array('createDirInRepo'));
        $fr->expects($this->any())
            ->method('createDirInRepo')
            ->will($this->returnValue(false));
        $this->assertFalse($fr->saveInRepo("anything"));

        // trying to save into a file that exists: 'repo7/foo.bar'
        mkdir(dirname(dirname(__FILE__)).DS.'repo7');
        file_put_contents(dirname(dirname(__FILE__)).DS.'repo7'.DS.'foo.bar', 'dummy content');

        $fr = $this->getmock('FileRetrieval', array('createDirInRepo', 'repoDir', 'localPath'));
        $fr->expects($this->any())
            ->method('createDirInRepo')
            ->will($this->returnValue(true));
        $fr->expects($this->any())
            ->method('repoDir')
            ->will($this->returnValue(dirname(dirname(__FILE__)).DS.'repo7'.DS));
        $fr->expects($this->any())
            ->method('localPath')
            ->will($this->returnValue('foo.bar'));
        $this->assertFalse($fr->saveInRepo("anything"));

        unlink(dirname(dirname(__FILE__)).DS.'repo7'.DS.'foo.bar');
        rmdir(dirname(dirname(__FILE__)).DS.'repo7');


    }

    public function testLazyRetrieval(){
        $this->assertTrue(method_exists('FileRetrieval', 'lazyRetrieval'));
        $fileContent = $this->createRepo('repo4');
        // retrieves url which local copy exists
        $fr = new FileRetrieval;
        $fr->setUrl('http://www.test.com/test1/foo.bar');
        $fr->setRepoDir(dirname(dirname(__FILE__)).DS.'repo4'.DS);
        $this->assertTrue($fr->localCopyExists());
        $this->assertEquals($fileContent, $fr->lazyRetrieval());
        $this->removeRepo('repo4');

        //retrieves url which local copy does not exist
        // first, imitate url 
        $startInfo = $this->createExternalUrl('webImitation4');
        $fileContent = $startInfo['content'];
        $fileName = $startInfo['filename']; 

        // prepare to retrieve from the imitated url
        $fr = new FileRetrieval;
        $fr->setUrl("http://localhost/filtro/webImitation4/".$fileName);
        $fr->setRepoDir(dirname(dirname(__FILE__)).DS.'repo5'.DS);

        // create dirs to save in the repo
        mkdir(dirname(dirname(__FILE__)).DS.'repo5');
        mkdir(dirname(dirname(__FILE__)).DS.'repo5'.DS.'localhost');
        mkdir(dirname(dirname(__FILE__)).DS.'repo5'.DS.'localhost'.DS.'filtro');
        mkdir(dirname(dirname(__FILE__)).DS.'repo5'.DS.'localhost'
            .DS.'filtro'.DS.'webImitation4');

        $this->assertEquals($fr->lazyRetrieval(), $fileContent);
        $this->assertTrue(file_exists(dirname(dirname(__FILE__)).DS.'repo5'.DS.'localhost'
            .DS.'filtro'.DS.'webImitation4'.DS.$fileName));

        unlink(dirname(dirname(__FILE__)).DS.'repo5'.DS.'localhost'.DS.'filtro'.DS.'webImitation4'.DS.$fileName);
        rmdir(dirname(dirname(__FILE__)).DS.'repo5'.DS.'localhost'.DS.'filtro'.DS.'webImitation4');
        rmdir(dirname(dirname(__FILE__)).DS.'repo5'.DS.'localhost'.DS.'filtro');
        rmdir(dirname(dirname(__FILE__)).DS.'repo5'.DS.'localhost');
        rmdir(dirname(dirname(__FILE__)).DS.'repo5');

        $this->removeExternalUrl(array('dirname' => 'webImitation4', 'filename' => $fileName));

 
    }
     

    public function testCreateDir(){
        // the repo is empty
        mkdir(dirname(dirname(__FILE__)).DS.'repo6');
        $fr = $this->getmock('FileRetrieval', array('localPath', 'repoDir'));
        $fr->expects($this->any())
            ->method('repoDir')
            ->will($this->returnValue(dirname(dirname(__FILE__)).DS.'repo6'.DS));
        $fr->expects($this->any())
            ->method('localPath')
            ->will($this->returnValue('a/b/c/d/index.html'));
        $this->assertTrue(method_exists('FileRetrieval', 'createDirInRepo'));
        $fr->createDirInRepo();
        $this->assertTrue(is_dir(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a'.DS.'b'.DS.'c'.DS.'d'));

        // trying to create the folders that are already present in the repo 
        $this->assertTrue($fr->createDirInRepo());
        $this->assertTrue(is_dir(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a'.DS.'b'.DS.'c'.DS.'d'));


        rmdir(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a'.DS.'b'.DS.'c'.DS.'d');
        rmdir(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a'.DS.'b'.DS.'c');
        // trying to create a set of nested dirs 'a/b/c/d/' in the case when one of the 
        // folders can not be created because it is already present a file with name 'c'
        file_put_contents(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a'.DS.'b'.DS.'c', "dummy content");

        $fr->createDirInRepo();
        $this->assertFalse($fr->createDirInRepo());

        unlink(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a'.DS.'b'.DS.'c');
        rmdir(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a'.DS.'b');
        rmdir(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a');
        rmdir(dirname(dirname(__FILE__)).DS.'repo6');

        // if(rmdir(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a'.DS.'b'.DS.'c'.DS.'d')){
        //     if(rmdir(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a'.DS.'b'.DS.'c')){
        //         if(rmdir(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a'.DS.'b')){
        //             if(rmdir(dirname(dirname(__FILE__)).DS.'repo6'.DS.'a')){
        //                 if(rmdir(dirname(dirname(__FILE__)).DS.'repo6')){
        //                     echo 'all tmp folders are removed';
        //                 }
        //             }
        //         }
        //     }
        // }



    }
 
}
?>