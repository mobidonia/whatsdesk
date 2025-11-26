<?php

namespace Modules\Translatechat\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Modules\Wpbox\Models\Contact;

class Main extends Controller
{
    
    private function callOpenAI($prompt)
    {
        //Get API key
        $open_ai_key = config('wpbox.openai_api_key');
        if(config('settings.is_demo',false)){
            $open_ai_key = config('wpbox.openai_api_key_demo');
        }

        if(strlen($open_ai_key)<5){
            //No API key
            return ['success' => false, 'message' => 'No API key configured'];
        }

        $dataTosend = [
            'model' => config('wpbox.openai_model'),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
        ];

        $openAIResponse = Http::timeout(400)->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $open_ai_key
        ])->post('https://api.openai.com/v1/chat/completions', $dataTosend);

        if(!$openAIResponse->ok()){
            return ['success' => false, 'message' => $openAIResponse->getBody()->getContents()];
        }
        
        return ['success' => true, 'content' => $openAIResponse->json()['choices'][0]['message']['content']];
    }

    public function convertStyle(Request $request)
    {
        $message = $request->input('message');
        $style = $request->input('style');
        $prompt = "Convert the following message to the specified style: " . $message . " Style: " . $style;
        $prompt .= "Reply in the same language as the message.";

        $response = $this->callOpenAI($prompt);
        return response()->json($response['success'] ? 
            ['success' => true, 'message' => $response['content']] : 
            $response);
    }

    public function askAI(Request $request)
    {
        $question = $request->input('message');
        $chat_id = $request->input('chat_id');

        //Find the contact
        $contact = Contact::find($chat_id);

        //Get all messages from the contact
        $messages = $contact->messages()->get();

        //Loop through the messages and make a prompt for the AI LLM to summarize the chat
        $prompt = "";
        $messageCount = 0;
        foreach($messages as $message){
            if($messageCount >= 30) break;
            
            if($message->is_message_by_contact){
                $prompt .= "Message from user: " . $message->value . "\n";
                $messageCount++;
            }else if($message->is_campign_messages.""=="0"){
                $prompt .= "Message from agent: " . $message->value . "\n"; 
                $messageCount++;
            }
        }

        //First summarize the chat
        $summaryResponse = $this->summarizeChat($chat_id);
        $summary = $summaryResponse->getData()->success ? $summaryResponse->getData()->summary : '';

        //Now ask the AI to answer the question
        $prompt = "Based on the following chat conversation, but not limited to it, answer the following question: " . $question . "\n\n" . $summary;
        $response = $this->callOpenAI($prompt);
        
        return response()->json($response['success'] ? 
            ['success' => true, 'answer' => $response['content'],'prompt' => $prompt,'summary' => $summary] : 
            $response);
    }

    public function summarizeChat($contact_id)
    {
        $contact = Contact::find($contact_id);

        //Get all messages from the contact
        $messages = $contact->messages()->get();

        //Loop through the messages and make a prompt for the AI LLM to summarize the chat
        $prompt = "";
        $messageCount = 0;
        foreach($messages as $message){
            if($messageCount >= 30) break;
            
            if($message->is_message_by_contact){
                $prompt .= "Message from user: " . $message->value . "\n";
                $messageCount++;
            }else if($message->is_campign_messages.""=="0"){
                $prompt .= "Message from agent: " . $message->value . "\n"; 
                $messageCount++;
            }
        }

        $prompt = "Please summarize this chat conversation:\n\n" . $prompt;
        $response = $this->callOpenAI($prompt);
        
        return response()->json($response['success'] ? 
            ['success' => true, 'summary' => $response['content']] : 
            $response);
    }
}
