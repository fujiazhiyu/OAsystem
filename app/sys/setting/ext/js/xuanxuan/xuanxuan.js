$(function()
{
    $('[name=debug]').change(function()
    {
        $('.viewDebug').toggle($(this).val() == 1);
    });
});

/**
 * create key for an entry.
 * 
 * @access public
 * @return void
 */
function createKey()
{
    var chars = '0123456789abcdefghiklmnopqrstuvwxyz'.split('');
    var key   = ''; 
    for(var i = 0; i < 32; i ++)
    {   
        key += chars[Math.floor(Math.random() * chars.length)];
    }   
    $('#key').val(key);

    $('#key').css({'margin-bottom' : 0, 'border-color' : ''});
    $('#keyLabel').remove();

    return false;
}

$(function()
{
    $('[name^=https]').change(function()
    {
        var value = $(this).val();
        $('#https').val(value);
        if(value == 'on')
        {
            $('.sslTR').show();
        }
        else
        {
            $('.sslTR').hide();
        }
    });

    $('#os').change(function()
    {
        var os = $(this).val();
        $('.download-package').attr('href', createLink('setting', 'downloadXXD', "type=package&os=" + os));
    });
    $('#os').change();
});
