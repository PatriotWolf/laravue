<template>
    <div class="ChatBox">
        <div class="chat">

            <div class="chat-title">
                <figure class="avatar">
                    <img alt="Bot's Avatar" src="img/robot01_90832.png" />
                </figure>
                <h1>My Bot</h1>
                <h2> HYE TALK TO ME !
                </h2>
            </div>

            <div class="messages" ref="chatContent">
                <div class="messages-content container">
                    <div  v-for="(message) in messages" :key="message.id">
                        <div class="message new" v-if="message.from === 'bot'">
                            <figure class="avatar">
                                <img alt="Bot's Avatar" src="img/robot01_90832.png" />
                            </figure>
                            <div v-html="message.text"></div>
                            <div class="btn-group" v-for="(button) in message.button" :key="button.id" v-if="message.button">
                                    <button
                                        class="btn btn-xs btn-primary"
                                        v-on:click="handleClick(button.text,message)"
                                        :disabled="message.isButtonDisabled"
                                    >
                                        {{button.text}}
                                    </button>

                            </div>
                            <div class="timestamp">
                            {{message.timestamp | formatDate}}
                            </div>
                        </div>

                        <div class="message message-personal  new" v-else>
                            <div v-html="message.text"></div>
                            <img :src="message.url" v-if="message.url" />
                            <div class="timestamp">
                            {{message.timestamp | formatDate}}
                            </div>
                        </div>
                    </div>
                    <div class="message loading new" v-if="isTyping">
                        <figure class="avatar">
                            <img alt="Bot's Avatar" src="img/robot01_90832.png" />
                        </figure>
                        <span />
                    </div>
                </div>
            </div>

            <div class="message-box">
                <form class="" @submit.prevent="addMessages">
                    <textarea
                    type="text"
                    class="message-input"
                    placeholder="Type message..."
                    ref="textInput"
                    v-model="text"
                    v-validate="'min:1'"
                    name="text"
                    v-on:keyup="handleKeypress"
                    :disabled="isTyping"
                    />

                    <button type="button" class="fileContainer" :disabled="isTyping">
                        File
                        <input
                        type="file"
                        class="input-pic"
                        @change="addImage"
                        />
                    </button>
                    <button
                    type="submit"
                    class="message-submit"
                    :disabled="isTyping"
                    >
                        Send
                    </button>
                </form>
            </div>
        </div>
        <audio ref="audio" controls>
            <source src="" type="mpeg">
        </audio>
    </div>
</template>
<script>
export default {
    name: 'ChatBox',
    props: {
        msg: String
    },
    mounted() {
        var audio = new Audio('http://66.90.93.122/ost/digimon-world-3/jlujtork/Main%20Lobby.mp3');
        audio.addEventListener('ended', function() {
            this.currentTime = 0;
            this.play();
        }, false);
        audio.volume = 0.5;
        audio.play();
        console.log('Component mounted.')
    },
    data() {
        return {
            text: '',
            isTyping: false,
            messages: [
                {
                    id: new Date().getTime(),
                    from: 'bot',
                    text: `Hye there!! can I help you?`,
                    isFile: false,
                    timestamp: new Date(),
                },
                {
                    id: new Date().getTime() + 1,
                    from: 'user',
                    text: 'Somebody!! Help!',
                    isFile: false,
                    timestamp: new Date(),
                },
                {
                    id: new Date().getTime() + 2,
                    from: 'bot',
                    text: 'But first, what you want to do?',
                    button: [
                        {
                            id: 0,
                            text: 'Sign-In',
                        },
                        {
                            id: 1,
                            text: 'Log-In',
                        },
                    ],
                    isButtonDisabled: false,
                    isFile: false,
                    timestamp: new Date(),
                },
            ],
        }
    },
    updated: function () {
        let container = this.$refs.chatContent;
        container.scrollTop = container.scrollHeight;
        this.$refs.textInput.focus();
    },
    filters: {
        formatDate: function (date) {
            let hours = date.getHours();
            let minutes = date.getMinutes();
            const ampm = hours >= 12 ? 'pm' : 'am';
            hours %= 12;
            hours = hours || 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? `0${minutes}` : minutes;
            const strTime = `${hours}:${minutes} ${ampm}`;
            return `${date.getMonth() + 1}/${date.getDate()}/${date.getFullYear()} ${strTime}`;
        }
    },
    methods: {
        addMessages() {
            if(this.text !== "") {
                const newObj = {
                    id: new Date().getTime(),
                    from: 'user',
                    text: this.text,
                    isFile: false,
                    timestamp: new Date(),
                };
                this.messages = [...this.messages, newObj];
                this.getMessage(this.text).then(response => {
                    const newObj = {
                        id: new Date().getTime(),
                        from: 'bot',
                        text: response.quote ? response.quote : response,
                        isFile: false,
                        timestamp: new Date(),
                    };
                    this.messages = [...this.messages, newObj];
                    this.isTyping = false;
                });
                this.text = '';
                this.isTyping = true;
            } else {
                alert("Please type a message");
            }
        },
        addImage(e){
            const reader = new FileReader();
            let url;
            reader.onload = () => {
                url = reader.result;
                const newObj = {
                    id: new Date().getTime(),
                    from: 'user',
                    url,
                    isFile: true,
                    timestamp: new Date(),
                };
                this.messages = [...this.messages, newObj];
                this.updated();
            };
            reader.readAsDataURL(e.target.files[0]);
        },
        getMessage(text){
            console.log(text)
            return fetch(`https://api.datamuse.com/words?rel_rhy=${text}`,
            {
                method: "GET",
            })
            .then((response) =>
                response.json()
            )
            .then((responseData) => {
                console.log(responseData)
                const rhymes = responseData.map(obj => {
                  return obj.word
                }).slice(0,3);
                console.log(rhymes)
                const speech = `${text} rhymes with ${(rhymes.length !== 0) ? rhymes : 'nothing'}`;
                console.log(speech);
                window.responsiveVoice.speak( speech, "UK English Male");
                return speech;
            })
            .catch(() => {
                return `there is something wrong with my end point`;
            });
        },
        handleClick: function(text,element){
            let array = [...this.messages]; // make a separate copy of the array
            const index = array.indexOf(element);
            array[index].isButtonDisabled = true;
            const newObj = {
                id: new Date().getTime(),
                from: 'user',
                text: text,
                isFile: false,
                timestamp: new Date(),
            };
            array = [...array, newObj];
            this.messages = array;
            this.isTyping = true;
            this.getMessage(text).then(response => {
                console.log(response)
                const newObj = {
                    id: new Date().getTime(),
                    from: 'bot',
                    text: response.quote ? response.quote : response,
                    isFile: false,
                    timestamp: new Date(),
                };
                this.messages = [...this.messages, newObj];
                this.isTyping = false;
            });
        },
        handleKeypress: function(e) {
        // console.log(this.messages)
            if (e.key === 'Enter' && !e.shiftKey) {
                let isEmpty = (!this.text || /^\s*$/.test(this.text))
                if(!isEmpty) {
                    const newObj = {
                        id: new Date().getTime(),
                        from: 'user',
                        text: this.text,
                        isFile: false,
                        timestamp: new Date(),
                    };
                    this.messages = [...this.messages, newObj];
                    this.getMessage(this.text).then(response => {
                        const newObj = {
                            id: new Date().getTime(),
                            from: 'bot',
                            text: response.quote ? response.quote : response,
                            isFile: false,
                            timestamp: new Date(),
                        };
                        this.messages = [...this.messages, newObj];
                        this.isTyping = false;
                    });
                    this.isTyping = true;
                } else{
                    alert("Please type a message")
                }
                this.text = '';
            }
        },
    }
}
</script>
<style scoped>
/*--------------------
    Chat
    --------------------*/
    .chat {
      position: absolute;
      top: 40%;
      left: 50%;
      -webkit-transform: translate(-50%, -50%);
              transform: translate(-50%, -50%);
      width: 300px;
      height: 80vh;
      max-height: 500px;
      z-index: 2;
      overflow: hidden;
      box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
      background: rgba(0, 0, 0, 0.5);
      border-radius: 20px;
      display: -webkit-box;
      display: -webkit-flex;
      display: -ms-flexbox;
      display: flex;
      -webkit-box-pack: justify;
      -webkit-justify-content: space-between;
          -ms-flex-pack: justify;
              justify-content: space-between;
      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
      -webkit-flex-direction: column;
          -ms-flex-direction: column;
              flex-direction: column;
    }
    /*--------------------
    Chat Title
    --------------------*/
    .chat-title {
      -webkit-box-flex: 0;
      -webkit-flex: 0 1 45px;
          -ms-flex: 0 1 45px;
              flex: 0 1 45px;
      position: relative;
      z-index: 2;
      background: rgba(0, 0, 0, 0.2);
      color: #fff;
      text-transform: uppercase;
      text-align: left;
      padding: 10px 10px 10px 50px;
    }
    .chat-title h1, .chat-title h2 {
      font-weight: normal;
      font-size: 10px;
      margin: 0;
      padding: 0;
    }
    .chat-title h2 {
      color: rgba(255, 255, 255, 0.5);
      font-size: 8px;
      letter-spacing: 1px;
    }
    .chat-title .avatar {
      position: absolute;
      z-index: 1;
      top: 8px;
      left: 9px;
      border-radius: 30px;
      width: 30px;
      height: 30px;
      overflow: hidden;
      margin: 0;
      padding: 0;
      border: 2px solid rgba(255, 255, 255, 0.24);
    }
    .chat-title .avatar img {
      width: 100%;
      height: auto;
    }


    /*--------------------
    Messages
    --------------------*/
    .messages {
      -webkit-box-flex: 1;
      -webkit-flex: 1 1 auto;
          -ms-flex: 1 1 auto;
              flex: 1 1 auto;
      color: rgba(255, 255, 255, 0.5);
      overflow-y: auto;
      overflow-x: hidden;
      position: relative;
      width: 100%;
    }
    .messages .messages-content {
    /*  position: absolute;*/
      top: 0;
      left: 0;
      height: 101%;
      width: 100%;
    }
    .messages .message {
      white-space: pre-line;
      clear: both;
      float: left;
      padding: 6px 10px 7px;
      border-radius: 10px 10px 10px 0;
      background: rgba(0, 0, 0, 0.3);
      margin: 8px 0;
      font-size: 11px;
      line-height: 1.4;
      margin-left: 35px;
      position: relative;
      text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
    }
    .messages .message .timestamp {
      position: absolute;
      bottom: -15px;
      font-size: 9px;
      color: rgba(255, 255, 255, 0.3);
    }
    .messages .message::before {
      content: '';
      position: absolute;
      bottom: -6px;
      border-top: 6px solid rgba(0, 0, 0, 0.3);
      left: 0;
      border-right: 7px solid transparent;
    }
    .messages .message .avatar {
      position: absolute;
      z-index: 1;
      bottom: -15px;
      left: -35px;
      border-radius: 30px;
      width: 30px;
      height: 30px;
      overflow: hidden;
      margin: 0;
      padding: 0;
      border: 2px solid rgba(255, 255, 255, 0.24);
    }
    .messages .message .avatar img {
      width: 100%;
      height: auto;
    }
    .messages .message.message-personal {
      float: right;
      color: #fff;
      text-align: right;
      background: -webkit-linear-gradient(330deg, #248A52, #257287);
      background: linear-gradient(120deg, #248A52, #257287);
      border-radius: 10px 10px 0 10px;
    }
    .messages .message.message-personal::before {
      left: auto;
      right: 0;
      border-right: none;
      border-left: 5px solid transparent;
      border-top: 4px solid #257287;
      bottom: -4px;
    }
    .messages .message:last-child {
      margin-bottom: 30px;
    }
    .messages .message.new {
      -webkit-transform: scale(0);
              transform: scale(0);
      -webkit-transform-origin: 0 0;
              transform-origin: 0 0;
      -webkit-animation: bounce 500ms linear both;
              animation: bounce 500ms linear both;
    }
    .messages .message.loading::before {
      position: absolute;
      top: 50%;
      left: 50%;
      -webkit-transform: translate(-50%, -50%);
              transform: translate(-50%, -50%);
      content: '';
      display: block;
      width: 3px;
      height: 3px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      z-index: 2;
      margin-top: 4px;
      -webkit-animation: ball 0.45s cubic-bezier(0, 0, 0.15, 1) alternate infinite;
              animation: ball 0.45s cubic-bezier(0, 0, 0.15, 1) alternate infinite;
      border: none;
      -webkit-animation-delay: .15s;
              animation-delay: .15s;
    }
    .messages .message.loading span {
      display: block;
      font-size: 0;
      width: 20px;
      height: 10px;
      position: relative;
    }
    .messages .message.loading span::before {
      position: absolute;
      top: 50%;
      left: 50%;
      -webkit-transform: translate(-50%, -50%);
              transform: translate(-50%, -50%);
      content: '';
      display: block;
      width: 3px;
      height: 3px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      z-index: 2;
      margin-top: 4px;
      -webkit-animation: ball 0.45s cubic-bezier(0, 0, 0.15, 1) alternate infinite;
              animation: ball 0.45s cubic-bezier(0, 0, 0.15, 1) alternate infinite;
      margin-left: -7px;
    }
    .messages .message.loading span::after {
      position: absolute;
      top: 50%;
      left: 50%;
      -webkit-transform: translate(-50%, -50%);
              transform: translate(-50%, -50%);
      content: '';
      display: block;
      width: 3px;
      height: 3px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      z-index: 2;
      margin-top: 4px;
      -webkit-animation: ball 0.45s cubic-bezier(0, 0, 0.15, 1) alternate infinite;
              animation: ball 0.45s cubic-bezier(0, 0, 0.15, 1) alternate infinite;
      margin-left: 7px;
      -webkit-animation-delay: .3s;
              animation-delay: .3s;
    }


    /*--------------------
    Message Box
    --------------------*/
    .message-box {
      -webkit-box-flex: 0;
      -webkit-flex: 0 1 40px;
          -ms-flex: 0 1 40px;
              flex: 0 1 40px;
      width: 100%;
      background: rgba(0, 0, 0, 0.3);
      padding: 10px;
      position: relative;
    }
    .message-box .message-input {
      background: none;
      border: none;
      outline: none !important;
      resize: none;
      color: rgba(255, 255, 255, 0.7);
      font-size: 11px;
      height: 17px;
      margin: 0;
      padding-right: 20px;
      width: 265px;
    }
    .message-box textarea:focus:-webkit-placeholder {
      color: transparent;
    }
    .message-box .message-submit {
      position: absolute;
      z-index: 1;
      top: 9px;
      right: 10px;
      color: #fff;
      border: none;
      background: #248A52;
      font-size: 10px;
      text-transform: uppercase;
      line-height: 1;
      padding: 6px 10px;
      border-radius: 10px;
      outline: none !important;
      -webkit-transition: background .2s ease;
      transition: background .2s ease;
    }
    .message-box .message-submit:hover {
      background: #1D7745;
    }
    /*--------------------
    Bounce
    --------------------*/
    @-webkit-keyframes bounce {
      0% {
        -webkit-transform: matrix3d(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      4.7% {
        -webkit-transform: matrix3d(0.45, 0, 0, 0, 0, 0.45, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.45, 0, 0, 0, 0, 0.45, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      9.41% {
        -webkit-transform: matrix3d(0.883, 0, 0, 0, 0, 0.883, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.883, 0, 0, 0, 0, 0.883, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      14.11% {
        -webkit-transform: matrix3d(1.141, 0, 0, 0, 0, 1.141, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1.141, 0, 0, 0, 0, 1.141, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      18.72% {
        -webkit-transform: matrix3d(1.212, 0, 0, 0, 0, 1.212, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1.212, 0, 0, 0, 0, 1.212, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      24.32% {
        -webkit-transform: matrix3d(1.151, 0, 0, 0, 0, 1.151, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1.151, 0, 0, 0, 0, 1.151, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      29.93% {
        -webkit-transform: matrix3d(1.048, 0, 0, 0, 0, 1.048, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1.048, 0, 0, 0, 0, 1.048, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      35.54% {
        -webkit-transform: matrix3d(0.979, 0, 0, 0, 0, 0.979, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.979, 0, 0, 0, 0, 0.979, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      41.04% {
        -webkit-transform: matrix3d(0.961, 0, 0, 0, 0, 0.961, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.961, 0, 0, 0, 0, 0.961, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      52.15% {
        -webkit-transform: matrix3d(0.991, 0, 0, 0, 0, 0.991, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.991, 0, 0, 0, 0, 0.991, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      63.26% {
        -webkit-transform: matrix3d(1.007, 0, 0, 0, 0, 1.007, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1.007, 0, 0, 0, 0, 1.007, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      85.49% {
        -webkit-transform: matrix3d(0.999, 0, 0, 0, 0, 0.999, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.999, 0, 0, 0, 0, 0.999, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      100% {
        -webkit-transform: matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
    }
    @keyframes bounce {
      0% {
        -webkit-transform: matrix3d(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      4.7% {
        -webkit-transform: matrix3d(0.45, 0, 0, 0, 0, 0.45, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.45, 0, 0, 0, 0, 0.45, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      9.41% {
        -webkit-transform: matrix3d(0.883, 0, 0, 0, 0, 0.883, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.883, 0, 0, 0, 0, 0.883, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      14.11% {
        -webkit-transform: matrix3d(1.141, 0, 0, 0, 0, 1.141, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1.141, 0, 0, 0, 0, 1.141, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      18.72% {
        -webkit-transform: matrix3d(1.212, 0, 0, 0, 0, 1.212, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1.212, 0, 0, 0, 0, 1.212, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      24.32% {
        -webkit-transform: matrix3d(1.151, 0, 0, 0, 0, 1.151, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1.151, 0, 0, 0, 0, 1.151, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      29.93% {
        -webkit-transform: matrix3d(1.048, 0, 0, 0, 0, 1.048, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1.048, 0, 0, 0, 0, 1.048, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      35.54% {
        -webkit-transform: matrix3d(0.979, 0, 0, 0, 0, 0.979, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.979, 0, 0, 0, 0, 0.979, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      41.04% {
        -webkit-transform: matrix3d(0.961, 0, 0, 0, 0, 0.961, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.961, 0, 0, 0, 0, 0.961, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      52.15% {
        -webkit-transform: matrix3d(0.991, 0, 0, 0, 0, 0.991, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.991, 0, 0, 0, 0, 0.991, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      63.26% {
        -webkit-transform: matrix3d(1.007, 0, 0, 0, 0, 1.007, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1.007, 0, 0, 0, 0, 1.007, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      85.49% {
        -webkit-transform: matrix3d(0.999, 0, 0, 0, 0, 0.999, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(0.999, 0, 0, 0, 0, 0.999, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
      100% {
        -webkit-transform: matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
                transform: matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1);
      }
    }
    @-webkit-keyframes ball {
      from {
        -webkit-transform: translateY(0) scaleY(0.8);
                transform: translateY(0) scaleY(0.8);
      }
      to {
        -webkit-transform: translateY(-10px);
                transform: translateY(-10px);
      }
    }
    @keyframes ball {
      from {
        -webkit-transform: translateY(0) scaleY(0.8);
                transform: translateY(0) scaleY(0.8);
      }
      to {
        -webkit-transform: translateY(-10px);
                transform: translateY(-10px);
      }
    }
    ::-webkit-scrollbar-track
    {
        -webkit-box-shadow: inset 0 0 2px rgba(0,0,0,0.3);
        background-color: rgba(0, 0, 0, 0.075);
    }

    ::-webkit-scrollbar
    {
        width: 2px;
        background-color: rgba(0, 0, 0, 0.075);
    }

    ::-webkit-scrollbar-thumb
    {
        background-color: #000000;
    }
    .fileContainer {
        overflow: hidden;
        position: relative;
    }

    .fileContainer [type=file] {
        cursor: inherit;
        display: block;
        font-size: 999px;
        filter: alpha(opacity=0);
        min-height: 100%;
        min-width: 100%;
        opacity: 0;
        position: absolute;
        right: 0;
        text-align: right;
        top: 0;
    }

    /* Example stylistic flourishes */

    .fileContainer {
            position: absolute;
    z-index: 1;
    top: 9px;
    right: 61px;
    color: #fff;
    border: none;
    background: #248A52;
    font-size: 10px;
    text-transform: uppercase;
    line-height: 1;
    padding: 6px 10px;
    border-radius: 10px;
    outline: none !important;
    -webkit-transition: background .2s ease;
    transition: background .2s ease;
    }

    .fileContainer [type=file] {
        cursor: pointer;
    }
    .btn-xs {
        padding: 1px 5px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
    }
    img{
        width: 100%;
    }
</style>
