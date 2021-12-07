<?php

namespace App\Http\Controllers;

// text to speech
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Aws\Ssm\SsmClient;

use Exception;

class AudioController extends Controller
{

    const CARD = 1;
    const EXAMPLE =2;

    const URL = 1;
    const FS = 2;
    const TTS_FOLDER = "audio";


    private static function getTargetFolder($pathType, $audioType, $id){

        // set separator
        $sep = ($pathType == self::FS ? DIRECTORY_SEPARATOR : "/");

        // get default path
        $folder = ($pathType == self::FS ? public_path() : '');

        // add audio folder
        $folder .= $sep . self::TTS_FOLDER;

        // add audio type to path
        $folder .= $sep . ($audioType == self::CARD ? 'c' : 'e');

        // create subfolders based on ID
        for ($i = 0; $i < strlen($id); $i++){
            $folder .= $sep . substr($id, $i, 1);
        }

        // return target path
        return [
            "folder" => $folder,
            "file" => $folder . $sep . $audioType . '_' . $id . '.mp3',
        ];

    }

    private static function checkType($type){
        if (($type != self::CARD) && ($type != self::EXAMPLE)){
            throw new Exception('generateAudioFile: unknown type ' . $type);
        }

    }

    private static function checkSymbol($symbol){
        if (empty($symbol)){
            throw new Exception('generateAudioFile: empty text.');;
        }
    }

    private static function checkId($id){
        if (!is_int($id) || $id < 1){
            throw new Exception('generateAudioFile: id is not an positive integer.');;
        }
    }

    public static function existsAudioFile($type, $id){
        $filename = self::getTargetFolder(self::FS, $type, $id)["file"];
        return file_exists($filename);
    }

    public static function generateAudioFile($type, $id, $symbol){

        self::checkSymbol($symbol);
        self::checkType($type);
        self::checkId($id);

        $googleAppCred = env('GOOGLE_APPLICATION_CREDENTIALS', '');

        if ($googleAppCred == ""){

            $jsonFile = base_path() . DIRECTORY_SEPARATOR . "aws.json";

            $aws = new SsmClient([
                'version' => 'latest',
                'region' => 'eu-west-1',
            ]);

            $data = $aws->getParameter([
                'Name' => 'flashcard-googleapi',
                'WithDecryption' => true,
            ]);

            file_put_contents($jsonFile, $data['Parameter']['Value']);

        } else {

            $jsonFile = base_path() . DIRECTORY_SEPARATOR . $googleAppCred;
            if (!file_exists($jsonFile)){
                throw new Exception('Google Cloud Credential missing. ' .
                    'Please check the GOOGLE_APPLICATION_CREDENTIALS variable ' .
                    ' in the .env file');
            }
        }
        
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $jsonFile);

        /** Uncomment and populate these variables in your code */
        $text = $symbol;

        // create client object
        $client = new TextToSpeechClient();

        $input_text = (new SynthesisInput())->setText($text);

        // note: the voice can also be specified by name
        // names of voices can be retrieved with $client->listVoices()
        $voice = (new VoiceSelectionParams())
            ->setLanguageCode('cmn-TW')
            ->setSsmlGender(SsmlVoiceGender::FEMALE);

        $audioConfig = (new AudioConfig())->setAudioEncoding(AudioEncoding::MP3);

        $response = $client->synthesizeSpeech($input_text, $voice, $audioConfig);
        $audioContent = $response->getAudioContent();

        $client->close();

        $target = self::getTargetFolder(self::FS, $type, $id);

        // create target folder if not exists
        if (!file_exists($target["folder"])){
            mkdir($target["folder"], 0755, true);
        }

        // store file (overwrites if file already exists)
        file_put_contents($target["file"], $audioContent);

    }

    public static function getBase64AudioFile($type, $id){

        self::checkType($type);
        self::checkId($id);

        $fileName =  self::getTargetFolder(self::FS, $type, $id)["file"];

        if (file_exists($fileName)){
            $content = file_get_contents($fileName);
            return base64_encode($content);
        } else {
            return '';
        }

    }

    public static function getAudioFilePath($type, $id){

        self::checkType($type);
        self::checkId($id);

        // Ok to use "/" as the separator in the URL is not OS specific
        return  [
            'url' => self::getTargetFolder(self::URL, $type, $id)["file"],
            'fs' => self::getTargetFolder(self::FS, $type, $id)["file"],
        ];

    }

    public static function deleteAudioFile($type, $id){

        self::checkType($type);
        self::checkId($id);

        $fileName =  self::getTargetFolder(self::FS, $type, $id)["file"];

        if (file_exists($fileName)){
            unlink($fileName);
        }

    }

}
