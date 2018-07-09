var alx_oInput;
function alxColor(arParams)
{
        this.arParams = arParams;
        alx_oInput = this.arParams.oInput;
        alx_oInput.type = 'text';
        alx_oInput.className = 'iColorPicker';
        alx_oInput.size = 19;
        iColorPicker();
}