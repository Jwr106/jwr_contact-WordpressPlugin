jQuery(document).ready(function($){
    $('#contact').click(function(){
        let html = `
            <div class="contact">
                <input class="input" name="email" placeholder="Email" autocomplete="off">
                <input class="input" name="phone" placeholder="Phone" autocomplete="off">
                <textarea class="textarea" name="message" placeholder="Message"></textarea>
                <div class="btn-wrap"><input class="button" type="button" value="Submit"></div>
            </div>
        `;
        layer.open({
            type: 1, 
            content: html,
            title:'Contact Us'
        });
    });

    $(document).on('click','.button',function(){
        let email = $('input[name=email]').val();
        let phone = $('input[name=phone]').val();
        let message = $('textarea[name=message]').val();
        let data = {
            email:email,
            phone:phone,
            message:message,
            action:'contact_us',
        };
        let index = layer.load(1);
        $.post(contact.ajax_url, data, function(rsp){
            layer.close(index);
            if(rsp.code =='1'){
                layer.msg(contact.success, {icon: 1});
            }else{
                layer.msg(contact.fail, {icon: 5});
            }
        },'JSON');
    })
});