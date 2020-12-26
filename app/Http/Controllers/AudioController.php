<?php

namespace App\Http\Controllers;

// text to speech
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

use Exception;

class AudioController extends Controller
{

    const CARD = 1;
    const EXAMPLE =2;
    const TTS_FOLDER = "audio";


    private static function getTargetFolder(){
        return public_path() . DIRECTORY_SEPARATOR . self::TTS_FOLDER;
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

    public static function generateAudioFile($type, $id, $symbol){

        self::checkSymbol($symbol);
        self::checkType($type);
        self::checkId($id);

        $credentials = base_path() . DIRECTORY_SEPARATOR . 
            env('GOOGLE_APPLICATION_CREDENTIALS', '');
        
        // Check if google cloud credential file exists
        if (!file_exists($credentials)){
            throw new Exception('Google Cloud Credential missing. ' .
                'Please check the GOOGLE_APPLICATION_CREDENTIALS variable ' .
                ' in the .env file');
        }

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentials);

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

        // create target folder if not exists
        $folder = self::getTargetFolder();
        if (!file_exists($folder)){
            mkdir($folder, 0755, true);
        }

        // generate file name
        $fileName =  $folder . DIRECTORY_SEPARATOR . $type . '_' . $id . '.mp3';

        // store file (overwrites if file already exists)
        file_put_contents($fileName, $audioContent);

    }

    public static function getBase64AudioFile($type, $id){

        self::checkType($type);
        self::checkId($id);

        $fileName =  self::getTargetFolder() . DIRECTORY_SEPARATOR . $type . '_' . $id . '.mp3';

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
            'url' => "/" . self::TTS_FOLDER . "/" . $type . '_' . $id . '.mp3',
            'full' => self::getTargetFolder() . "/" . $type . '_' . $id . '.mp3',
        ];

    }

    public static function deleteAudioFile($type, $id){

        self::checkType($type);
        self::checkId($id);

        $fileName =  self::getTargetFolder() . DIRECTORY_SEPARATOR . $type . '_' . $id . '.mp3';

        if (file_exists($fileName)){
            unlink($fileName);
        }

    }

}
