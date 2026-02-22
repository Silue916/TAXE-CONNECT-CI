<?php

namespace Epaphrodites\epaphrodites\shares\ajaxTemplate;

trait chatBot{
  
    /**
     * @param array $data
     * @param string $submit
     * @param string $chatBotName
     * @return string
     */
    public function chatMessageContent(
        array $data, 
        string $submit, 
        string $chatBotName = "EpaphroditesBot"
    ):string {
        $data = array_reverse($data);
        $html = '<div class="chat-container">';
        $firstItem = true;
        
        foreach ($data as $key => $value) {
            $additionalClass = $firstItem ? 'answers' : '';
            $html .= '
            <div class="chat-item">
                <div class="msg">
                    <strong>You :</strong>
                    <p class="user-msg">' . $data[$key]["question"] . '</p>
                </div>
                <div class="msg">
                    <strong>'.$chatBotName.' :</strong>
                    <div class="bot-msg-'.$additionalClass.'">
                        <p>' . nl2br($data[$key]["answers"]) . '</p>
                    </div>
                </div>
            </div>';
        
            $firstItem = false;
        }
        
        $html .= '</div>';
        
        if ($submit !== '') {
            $html .= '<script>
            const delay = 10;
            
            function displayText(element, text) {
                let index = 0;
                const display = () => {
                    element.innerHTML = text.slice(0, index);
                    index++;
                    if (index <= text.length) {
                        setTimeout(display, delay);
                    }
                };
            
                display();
            }
            
            const botMessages = document.querySelectorAll(".bot-msg-answers");
            botMessages.forEach((message) => {
                displayText(message, message.innerHTML);
            });
            </script>';
        }
        
        return $html;
    }


    public function otherChatMessageContent(
        string $send
    ): string{

        $html = 
        <<<HTML
        <div class="chat-message user">
            <strong>You :</strong> $send
        </div>
        <div class="chat-message bot">
            <strong>Ollama :</strong> <span id="live-response"></span>
        </div>
        <div id="chat-end"></div>
        HTML;
    
        return $html;
    }
    
    
}