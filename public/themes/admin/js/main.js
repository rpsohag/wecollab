$(document).ready(function () {

    //Disable all buttons when a submit is clicked
    $('[type="submit"]').click(function(){
        var btn = $('[type="submit"]');
        btn.addClass('disabled');

        setTimeout(function(){
            btn.removeClass('disabled');
        }, 5000);
    });

    // Target _blank intranet v1.0
    $('.sidebar-menu a[href*="https://intranet"]').attr('target', '_blank');

    // Menu mobile
    $('a.sidebar-toggle').click(function(e) {
        e.preventDefault();

        var sidebar = $('aside.main-sidebar');

        if(sidebar.css('transform') == 'none') {
            sidebar.css({
                'transform': 'translate(-230px, 0)',
                '-webkit-transform': 'translate(-230px, 0)'
            });
        } else {
            sidebar.css({
                'transform': 'initial'
            });
        }
    });

    // Tabs cookies
    $('section.content .nav-tabs [data-toggle="tab"]').click(function() {
        $.cookie('nav-tab', $(this).attr('href'));
    });
    if($.cookie('nav-tab')) {
        var tab = $.cookie('nav-tab');
        var navTabs = $('section.content .nav-tabs [data-toggle="tab"]');
        var tabPane = $('section.content .tab-content .tab-pane');
        
        if(navTabs.length > 0) {
            navTabs.parent().removeClass('active');
            $('section.content .nav-tabs [href='+tab+']').parent().addClass('active');
        }

        if(tabPane.length > 0) {
            tabPane.removeClass('active');
            $('section.content .tab-content .tab-pane'+tab).addClass('active');
        }
    }

    // Start
    $('[data-slug="source"]').each(function(){
	    $(this).slug();
	});

    $(document).ajaxStart(function() { Pace.restart(); });

    Mousetrap.bind('f1', function() {
        window.open('https://www.we-com.it', '_blank');
    });

    // Modal default
    $('#modal-default').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        modal.find('.modal-dialog').removeClass('modal-lg').removeClass('modal-sm');
        modal.find('.modal-title').html(button.data('title'));

        if(button.attr('data-size')) {
            modal.find('.modal-dialog').addClass(button.attr('data-size'));
        }

        if(button.attr('data-ajax') == 'true') {
            modal.addClass('form-ajax');
        }

        if(button.attr('data-type') == 'iframe') {
            modal.find('.modal-body').html('<iframe class="modal-iframe" src="' + button.attr('data-action') + '"></iframe>');
        } else {
            $.get(button.data('action'), function(data) {
                var content = '';
                var html = $(data.trim());
                if(html.find('aside.content-wrapper').length > 0)
                  html = $(data.trim()).find('aside.content-wrapper');

                if(button.attr('data-javascript') == 'false') {
                    html.find('script').remove();
                }

                if(button.attr('data-element')) {
                    if(button.attr('data-parent')) {
                        content = html.find(button.attr('data-element')).parent().html();
                    } else {
                        content = html.find(button.attr('data-element')).html();
                    }
                } else {
                    content = html.prop('outerHTML');
                }

                modal.find('.modal-body').html(content);
                bootJs();

                if(button.attr('data-form-disabled') == 'true') {
                    modal.find('.modal-body input, .modal-body select, .modal-body textarea').attr('disabled', 'disabled');
                    modal.find('.modal-body [type="submit"]').remove();
                }
                if(button.attr('data-form-remove') == 'true') {
                    modal.find('.modal-body form').remove();
                }
            });
        }
    });
    $('#modal-default').on('hidden.bs.modal', function () {
        var modal = $(this);

        modal.find('.modal-body').html('');
    });

    // BOOT
    bootJs();
});

function currency(element) {
    var currency = $(element).data('currency');
    var number = cleanCurrency($(element).val());

    var value = formatNumber(number, currency);
    // $.get('admin/wecore/get-currency-ajax/'+number+'/'+currency+'')
    //   .done(function(val) {
    //     return $(element).val(val);
    //   });

    $(element).val(value);
}

function formatNumber(amount, currency, decimalCount = 2, decimal = ",", thousands = ".") {
  try {
    decimalCount = Math.abs(decimalCount);
    decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

    const negativeSign = amount < 0 ? "-" : "";

    let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
    let j = (i.length > 3) ? i.length % 3 : 0;

    return currency + ' ' + negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
  } catch (e) {
    console.log(e)
  }
}

function formatNumberOld(number, currency) {

    number = number.toString().replace(currency, '');
	  var num = number.trim();

    if(num.length == 0) {
        return currency + ' 0,00';
    }

	if((num.length > 6 && num.indexOf(',') !== -1) || (num.length > 3 && num.indexOf(',') === -1 && num.indexOf('.') === -1) || (num.length < 4 && num.indexOf(',') !== -1 && num.indexOf('.') !== -1)) {
		number = number.replace(".", "");
		number = number.replace(",", ".");
		number = Number(number.trim());

		if(isNaN(number)) {
			return currency + ' 0,00';
		} else {
			return currency + ' ' + number.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').replace(".", ",").replace(",", ".");
		}
	} else {
		if(num.length < 4 && num.indexOf(',') === -1 && num.indexOf('.') === -1) {
            number = Number(number.trim());
            if(isNaN(number))
                number = 0;

			return currency + ' ' + number + ',00';
		} else {
            number = Number(number.replace(",", ".")).toFixed(2).toString();

            if(isNaN(number)) {
    			return currency + ' 0,00';
    		} else {
			    return currency + ' ' + number.replace(".", ",").trim();
        }
		}
	}
}

function cleanCurrency(number, currency = '€') {
    if(number !== undefined){
        number = number.toString()
        .replace('€', '')
        .replace(/\./g, '')
        .replace(',', '.')
        .trim();
    }
    return Number(number);
}

function eliminaFile(name, imgPlaceholder = 'http://via.placeholder.com/100x100') {
    $('#immagine-' + name).attr('src', imgPlaceholder);
    $('#elimina-' + name).attr('value', 1);
}

function responsive_filemanager_callback(field_id) {
    var field = $('#'+field_id);
    var hostName = window.location.protocol + '//' + window.location.hostname + '/';
    var url = field.val().replace(hostName, '');

    field.val(url);
}

function tinymceURLConverter(url, node, on_save, name) {
    var hostName = window.location.protocol + '//' + window.location.hostname + '/';
    var urlNew = url.replace(hostName, '');

    return urlNew;
}


/* Attività */
// Remove voce
function removeAttivitaVoce(el) {
    var vociCount = $('.attivita-voce').length;

    if(vociCount > 1) {
        if(confirm('ATTENZIONE: sicuro di voler rimuovere il lavoro?'))
            $(el).parent().parent().parent().remove();
    } else {
        alert('AVVISO: questo lavoro non può essere rimosso.');
    }
}

// Assegnatari
function getAttivitaAssegnatari(first = false) {
    var assegnatari = $('select[name="assegnatari_id[]"] option[selected="selected"]');
    var $selects = $('.lista-voci select.tags').selectize();

    $selects.each(function(i) {
        var select = $selects[i].selectize;
        var values = select.getValue();
        var valuesFirst = $('input[name="voci[' + i + '][users_selected]"').val();

        select.clearOptions();

        assegnatari.each(function() {
            var assegnatario = $(this);

            select.addOption({
                value:assegnatario.val(),
                text:assegnatario.text()
            });

            if(first) {
                if(valuesFirst.indexOf(assegnatario.val()) !== -1)
                    select.addItem(assegnatario.val());
            }
        });

        values.forEach(function(val) {
            select.addItem(val);
        });
    });
}

// Update stato
function updateAttivitaStato() {
    var stati = $('.voce-stato');
    var ctrlStato = stati.first().val();
    var ctrl = true;

    stati.each(function() {
        var statoVoce = $(this).val();

        if(ctrlStato != statoVoce)
            ctrl = false;
    });

    if(ctrl)
        $('select[name="stato"]').val(ctrlStato);
}

// Percentuale voci
function vociPercentuale(voceId = null) {
    var percentuale = $("#percentuale_completamento").slider();
    var stato = $('select[name="stato"]');
    var vociSlider = $('.attivita-voce input.slider');
    var vociCount = vociSlider.length;
    var vociSum = 0;
    var vociMedia = 0;

    vociSlider.each(function(i, voce) {
        vociSum += parseFloat($(voce).val());
    });

    vociMedia = vociSum / vociCount;

    percentuale.slider("setAttribute", "value", vociMedia);
    percentuale.slider("refresh");

    if(vociMedia == 100)
        stato.val(2);
    else
        stato.val(0);

    if(voceId != null) {
        var voceStato = $('select[name="voci['+voceId+'][stato]"]');
        var vocePercentuale = $('input[name="voci['+voceId+'][percentuale_completamento]"]');

        if(voceId != null && vocePercentuale.val() == 100)     
            voceStato.val(2);
        else
            voceStato.val(0);
    }
}

function copia(testo_id) {
    var url = $('#'+testo_id).attr('href');
    try {
        navigator.clipboard.writeText(url);
        console.log('Copiato nella clipboard!');
      } catch (err) {
        console.error('Impossibile da copiare: ', err);
      }
}

function clipboard_copy(testo) {
    try {
        navigator.clipboard.writeText(testo);
        console.log('Copiato nella clipboard!');
      } catch (err) {
        console.error('Impossibile da copiare: ', err);
      }
}

// Search in JSON
function findJSON(obj, searchField, searchVal) {
    var results = [];

    for(var i=0; i < obj.length; i++)
        if(obj[i][searchField] == searchVal)
            results.push(obj[i]);

    return results;
}