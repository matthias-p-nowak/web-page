<?php
namespace Code;

class Help{
    public static function ShowHelp(){
        echo <<< EOM
        <dialog id="show_help" x-action="replace">
        <h1>Simple web page help</h1>
        <ul>
        <li><b>Just editing:</b> 
        For simple edits without formatting, just click onto the word and change the text. When done, click somewhere else.
        <br >
        For more complex editing with changing formatting, headlines, paragraphs and similar, right click onto the place.
        The editor <em>TinyMCE</em> shows up with a few additional buttons on top.
         <ul>
         <li><b>More to edit</b> The amount of text in the editor will be extended to the next upper level.</li>
         <li><b>Duplicate</b> The displayed section will be duplicated, like when one wants to add a similar section.</li>
         <li><b>Save</b> Saves the bit</li>
         <li><b>Cancel</b> Aborts the edit</li>
         </ul>
        <hr >
        </li>
        <li><b>Page:</b> edit metadata like file name, title, description, styles, home page and also gives an overview over pages</li>
        <li><b>Pictures/Media:</b> manage pictures and media like PDF's including their url</li>
        <li><b>Logout:</b> return to normal view of the web-site like any other visitor</li>
        <li><b>Rewind:</b> restore web-site to an earlier state</li>
        </ul>
        Press 'Escape' to get out of editor or this help!
        </dialog>
        EOM;
    }
}