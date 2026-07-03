// for the mouse-over on the input-field change the css
    function changeStyleInput(input_id){
        console.log(input_id)
        const input_tag = document.getElementById(input_id)

        input_tag.style.borderRadius = "5px"
        input_tag.style.border = "2px solid white"
    }

    // for the mouse-leave on input-field reverse the css
    function resetStyleInput(input_id){
       const input_tag = document.getElementById(input_id)

       input_tag.style.borderRadius = "8px"
       input_tag.style.border = "1px solid #4B5563"
    }

    // for the mouse-over on the button change the css
    function changeStyleBtn(input_id){
        const btn_id = document.getElementById(input_id)

        btn_id.style.backgroundColor = "#4e8aea"
        btn_id.style.borderRadius = "5px"
        btn_id.style.border = "1px solid white"
    }

    // remove the message after 2 seconds
    function removeMessage(){
        const message_tag = document.getElementById("message")
        setTimeout(() => {
            message_tag.style.display = "none"
        }, 2000);
    }