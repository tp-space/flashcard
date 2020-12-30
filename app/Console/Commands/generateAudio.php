<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Label;

use App\Http\Controllers\AudioController;

use Exception;

class generateAudio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fc:genAudio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Audio files for cards and examples';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $result = [];
        $data = Label::select([
            'id', 
            'label', 
        ])->with([
            'cards',
            'cards.examples',
        ])->get();

        $total = 0;
        foreach ($data as $item){
            $numCards = count($item->cards);
            $numCardsChar = 0;
            $numExamples = 0;
            $numExamplesChar = 0;
            foreach($item->cards as $card){
                $numCardsChar += strlen($card->symbol);
                $numExamples += count($card->examples);
                foreach($card->examples as $example){
                    $numExamplesChar += strlen($example->example);
                }
            }
            $result[] = [
                'id' => $item->id,
                'label' => $item->label,
                'cards' => $numCards,
                'cards_char' => $numCardsChar,
                'examples' => $numExamples,
                'examples_char' => $numExamplesChar,
            ];
            $total += ($numCardsChar + $numExamplesChar);
        }

        $this->table([
            'ID', 
            'Label', 
            'Cards', 
            'Characters (cards)', 
            'Examples',
            'Characters (examples)', 
        ], $result);

        $this->line('Total number of characters (estimate) : ' . $total);

        $label = $this->ask('Enter Label ID (enter "all" to generate audio for all labels):');
        $withCards = $this->confirm('Generate audio for cards?', true);
        $withExamples = $this->confirm('Generate audio for examples?', true);
        $withOverwrite = $this->confirm('Overwrite existing audio files?', false);


        $cards = [];
        $examples = [];
        $total = 0;
        $labelName = '';
        foreach ($data as $item){

            if (($label == 'all') || ($label == $item->id)){

                $labelName = ($labelName == '' ? $item->label : 'all');

                foreach($item->cards as $card){

                    if ($withCards){
                        if (false == array_key_exists($card->id, $cards)){
                            if ($withOverwrite || (!AudioController::existsAudioFile(AudioController::CARD, $card->id))){
                                $cards[$card->id] = $card->symbol;
                                $total += strlen($card->symbol);
                            }
                        }
                    }
                    if ($withExamples){
                        foreach($card->examples as $example){
                            if (false == array_key_exists($example->id, $examples)){
                                if ($withOverwrite || (!AudioController::existsAudioFile(AudioController::EXAMPLE, $example->id))){
                                    $examples[$example->id] = $example->example;
                                    $total += strlen($example->example);
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->line('');
        $this->line('');
        $this->line('');
        $this->line('Label: ' . $labelName);
        $this->line('Generate Audio for Cards: ' . ($withCards ? 'yes' : 'no'));
        $this->line('Generate Audio for Examples: ' . ($withExamples ? 'yes' : 'no'));
        $this->line('Overwrite existing audio files:' . ($withOverwrite ? 'yes' : 'no'));
        $this->line('Total number of characters to generate audio for: ' . $total);

        if ($total == 0){
            $this->info('There are no characters to create audio files for. Command terminated');
        } else{
            if ($this->confirm('Do you want to generate audio files for above assets?', false)){

                try {

                    $bar = $this->output->createProgressBar(count($cards) + count($examples));
                    $bar->start();
                    foreach($cards as $id => $symbol){
                        AudioController::generateAudioFile(AudioController::CARD, $id, $symbol);
                        $bar->advance();
                        usleep(500000);
                    }

                    foreach($examples as $id => $example){
                        AudioController::generateAudioFile(AudioController::EXAMPLE, $id, $example);
                        $bar->advance();
                        usleep(500000);
                    }
                    $bar->finish();

                } catch(Exception $e){
                    $this->error($e->getMessage());
                }
                $this->info('OK');
            } else {
                $this->info('Abort');
            }
        }

        return 0;
    }
}
