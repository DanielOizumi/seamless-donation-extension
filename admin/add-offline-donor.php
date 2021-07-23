
<form id="admin-seamless-donations-form" name="admin-seamless-donations-form" enctype="multipart/form-data">
    <div class="seamless-donations-forms-error-message" style="display:none"></div>
    <div id="dgx-donate-form-donation-section" class="dgx-donate-form-section">
        <div id="donation_header">
            <h2>Information sur le don</h2>
        </div>
        <div id="header_desc">
            <p>$1 plante un arbre</p>
        </div>
        <div id="_dgx_donate_user_amount" class="aftertext seamless-donations-form-row other-donation-level">
            <div id="_dgx_donate_user_amount-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <div class="seamless-donations-col-25">
                <input type="text" name="_dgx_donate_user_amount" value="" placeholder="Montant" id="_dgx_donate_user_amount" data-validate="currency">
            </div>
        </div>
        <div>
            <div id="_dgx_donate_message" class="dgx-donate-message">
                <div id="_dgx_donate_message-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
                <div class="seamless-donations-col-25">
                    <input type="text" name="_dgx_donate_message" value="" placeholder="Mon message est..." maxlength="100">
                </div>
            </div>
        </div>
        <div>
            <div id="_dgx_donate_team" class="dgx-donate-team">
                <div id="_dgx_donate_team-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
                <div class="seamless-donations-col-25">
                    <input type="text" name="_dgx_donate_team" value="" placeholder="Équipe" maxlength="100">
                </div>
            </div>
        </div>
    </div>
    <div id="dgx-donate-form-tribute-section" class="dgx-donate-form-section">
        <div id="donation_header">
            <div id="donation_header-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <h2>Don honorifique</h2>
        </div>
        <div id="_dgx_donate_tribute_gift">
            <div id="_dgx_donate_tribute_gift-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <label>
                <input type="checkbox" name="_dgx_donate_tribute_gift" id="dgx-donate-tribute" data-reveal=".in-honor" data-conceal=".postal-acknowledgement, .conceal-state, .conceal-postcode, .conceal-province" data-check="_dgx_donate_honor_by_email">
            Cochez ici pour que ce don soit fait en l’honneur ou à la mémoire de quelqu’un</label>
        </div>
        <div id="_dgx_donate_memorial_gift" class="in-honor" style="display:none">
            <div id="_dgx_donate_memorial_gift-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <label>
                <input type="radio" name="_dgx_donate_tribute_gift_radio" checked="checked" value="0" id="dgx-donate-honor-gift">
            En l’honneur de …</label>
            <label>
                <input type="radio" name="_dgx_donate_tribute_gift_radio" value="1" id="dgx-donate-memory-gift">
            En mémoire de …</label>
        </div>
        <div id="_dgx_donate_honoree_name" class="seamless-donations-form-row in-honor" style="display:none">
            <div id="_dgx_donate_honoree_name-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <div class="seamless-donations-col-25">
                <label for="_dgx_donate_honoree_name"> </label>
            </div>
            <div class="seamless-donations-col-75">
                <input type="text" name="_dgx_donate_honoree_name" value="" size="20" placeholder="Nom de la personne honorée (champ obligatoire)" maxlength="100">
            </div>
        </div>
        <div id="_dgx_donate_honoree_email_name" class="seamless-donations-form-row in-honor email-acknowledgement" style="display:none">
            <div id="_dgx_donate_honoree_email_name-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <div class="seamless-donations-col-25">
                <label for="_dgx_donate_honoree_email_name"> </label>
            </div>
            <div class="seamless-donations-col-75">
                <input type="text" name="_dgx_donate_honoree_email_name" value="" size="20" placeholder="Courriel de remerciements à">
            </div>
        </div>
        <div id="_dgx_donate_honoree_email" class="seamless-donations-form-row in-honor email-acknowledgement" style="display:none">
            <div id="_dgx_donate_honoree_email-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <div class="seamless-donations-col-25">
                <label for="_dgx_donate_honoree_email"> </label>
            </div>
            <div class="seamless-donations-col-75">
                <input type="text" name="_dgx_donate_honoree_email" value="" size="20" placeholder="Courriel" data-validate="email">
            </div>
        </div>
    </div>
    <div id="dgx-donate-form-donor-section" class="dgx-donate-form-section">
        <div id="donation_header">
            <div id="donation_header-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <h2>Informations sur le donateur</h2>
        </div>
        <div id="_dgx_donate_image_div">
            <input type="file" id="_dgx_donate_image" name="_dgx_donate_image" accept=".gif,.jpg,.jpeg,.png" class="inputfile">
            <label for="_dgx_donate_image">Choisissez une photo de profil</label>
        </div>
        <div id="_dgx_donate_donor_first_name" class="seamless-donations-form-row">
            <div id="_dgx_donate_donor_first_name-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <div class="seamless-donations-col-25">
                <label for="_dgx_donate_donor_first_name"> </label>
            </div>
            <div class="seamless-donations-col-25">
                <input type="text" name="_dgx_donate_donor_first_name" value="" size="20" placeholder="Prénom" data-validate="required" maxlength="500">
            </div>
        </div>
        <div id="_dgx_donate_donor_last_name" class="seamless-donations-form-row">
            <div id="_dgx_donate_donor_last_name-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <div class="seamless-donations-col-25">
                <label for="_dgx_donate_donor_last_name"> </label>
            </div>
            <div class="seamless-donations-col-25">
                <input type="text" name="_dgx_donate_donor_last_name" value="" size="20" placeholder="Nom" data-validate="required" maxlength="500">
            </div>
        </div>
        <div id="_dgx_donate_donor_email" class="seamless-donations-form-row">
            <div id="_dgx_donate_donor_email-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <div class="seamless-donations-col-25">
                <label for="_dgx_donate_donor_email"> </label>
            </div>
            <div class="seamless-donations-col-75">
                <input type="text" name="_dgx_donate_donor_email" value="" size="20" placeholder="Courriel" data-validate="required,email" maxlength="500">
            </div>
        </div>
        <div id="_dgx_donate_donor_phone" class="seamless-donations-form-row">
            <div id="_dgx_donate_donor_phone-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <div class="seamless-donations-col-25">
                <label for="_dgx_donate_donor_phone"> </label>
            </div>
            <div class="seamless-donations-col-25">
                <input type="text" name="_dgx_donate_donor_phone" value="" size="20" placeholder="Téléphone" maxlength="20">
            </div>
        </div>
        <div id="_dgx_donate_anonymous">
            <div id="_dgx_donate_anonymous-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <label>
                <input type="checkbox" name="_dgx_donate_anonymous">
            Ne publiez pas mon nom. Je souhaite rester anonyme</label>
        </div>
    </div>
    <div id="dgx-donate-form-payment-section" class="dgx-donate-form-section">
        <div id="dgx-donate-pay-enabled" class="dgx-donate-pay-enabled">
            <div id="dgx-donate-pay-enabled-error-message" style="display:none" class="seamless-donations-error-message-field"></div>
            <input type="submit" name="dgx-donate-pay-enabled" value="Donnez maintenant">
        </div>
    </div>
</div>
<input type="hidden" id="Jcrop_x" name="Jcrop_x" value="">
<input type="hidden" id="Jcrop_y" name="Jcrop_y" value="">
<input type="hidden" id="Jcrop_w" name="Jcrop_w" value="">
<input type="hidden" id="Jcrop_h" name="Jcrop_h" value="">
</form>
<div id="image_prev_modal" class="burkina-modal">
    <div id="image_prev_container" class="burkina-modal-content" style="max-width:280px">
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" id="image_prev">
        <div class="image_prev_button">
            <span class="stunning-item-button greennature-button large">Ok</span>
        </div>
    </div>
</div>
<div id="image_loading_modal" class="burkina-modal">
    <div id="image_loading_container" class="burkina-modal-content" style="max-width:280px">
        <div id="image_loading"></div>
    </div>
</div>