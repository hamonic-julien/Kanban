var app = {

    //Propriété apiUrl contenant l'url avant la partie endpoint
    apiUrl: 'http://localhost/S6/S06-E04-backend-oKanbanban-hamonic-julien/public',

    init: function() {
        console.log('init');
        //Atelier : Vider les listes existantes au chargement du DOM
        $('#lists').empty();
        
        //Etape 3 : ajout du bouton d'ajout d eliste dans le DOM
        app.addListAddingBtn();

        //Atelier : appeler une méthode permettant de charger toutes les listes
        app.loadLists();

        //Etape 1 : vanilla => jQuery
        // Récupration de l'élément Modal pour ajouter une liste
        var $addListModalElement = $('#addListModal');
        // Récupration de l'élément Modal pour ajouter une carte
        var $addCardModalElement = $('#addCardModal');
        // Cibler tous les boutons de fermeture du modal
        $addListModalElement.find('.close').on('click', app.hiddeAddListModal);
        $addCardModalElement.find('.close').on('click', app.hiddeAddCardModal);

        //Etape 2
        $('h2').on('dblclick', app.handleDblClickOnListTitle);
        //on cible le formulaire d'ajout de liste
        //pour écouter l'évenement et lancer la fonction dédiée
        var $formListModal = $('#addListModal').find('form');
        $formListModal.on('submit', app.formAddListSubmit);

        //on cible le formulaire d'ajout de carte
        //pour écouter l'évenement et lancer la fonction dédiée
        var $formCardModal = $('#addCardModal').find('form');
        $formCardModal.on('submit', app.formAddCardSubmit);

    },

    //Méthode permettant d'afficher le formulaire d'ajout de list
    displayAddListModal: function() {
        //on vide l'input avant d'afficher le formulaire
        $('#list-name').val('');
        // Récupration de l'élément Modal
        //var modal = document.getElementById('addListModal');
        //var $modal.$('#addListModal');
        // Ajout de la classe active
        //modal.classList.add('is-active');
        $('#addListModal').addClass('is-active');
        //console.log('affiche formulaire');
    },

    //Méthode permettant d'afficher le formulaire d'ajout de carte
    displayAddCardModal: function() {
        console.log('affiche formulaire ajout carte')
        //on vide l'input avant d'afficher le formulaire
        $('#card-name').val('');
        // Récupération de l'élément Modal
        // Ajout de la classe active
        $('#addCardModal').addClass('is-active');
    },

    //Méthode permettant de masquer le formulaire d'ajout de list
    hiddeAddListModal: function() {
        // Retrait de la classe active
        $('#addListModal').removeClass('is-active');
    },

    //Méthode permettant de masquer le formulaire d'ajout de carte
    hiddeAddCardModal: function() {
        // Retrait de la classe active
        $('#addCardModal').removeClass('is-active');
    },

    //Méthode permettant de bloquer le fonctionnement à la soumission du formulaire ajout Liste
    formAddListSubmit: function(event) {
        // désactiver le fonctionnement par défaut
        event.preventDefault();
        // On récupère le formulaire
        var formElement = event.currentTarget;
        // Mais on veut la version jQuerysé de ce formulaire
        var $formElement = $(formElement);
        // On récupère l'élément input
        var $inputElement = $formElement.find('input');
        // On récupère ensuite sa valeur
        var inputValue = $inputElement.val();
        console.log('value input : ' + inputValue);
        // Appel Ajax sur le endpoint /lists/add
        $.ajax({
            url: app.apiUrl + '/lists/add',
            method: 'POST',
            dataType: 'json',
            data: {
                listName: inputValue
            }
            }).done(function(response) {
            // Si ça a fonctionné
            if (response.code == 1) {
                // appeler la méthode qui génère l'élément liste (mais ne l'ajoute pas encore dans le DOM)
                var $listElement = app.generateListElement(response.model.name, response.model.id);
                // Ajouter ce $listElement dans le DOM
                $listElement.insertBefore('#add-list-btn-column');
                // fermer la fenetre modal
                app.hiddeAddListModal();
            }
            else {
                alert(response.error);
            }
            }).fail(function() {
            alert('ajax failed');
            });
    },

    //Méthode permettant de bloquer le fonctionnement à la soumission du formulaire ajout carte
    formAddCardSubmit: function(event) {
        //je bloque le refresh de la page
        event.preventDefault();
        //je récupère la valeur de l'input
        var $inputElement = $('#card-name').val().trim();
        //Récupération de l'id de la liste où l'ajout de la carte est fait
        //TODO rendre dynamique $listId
        var $listId = 1;
        //var $listId = $(event.currentTarget).data('id');
        //console.log($(event.currentTarget));
        //Envoyer vers la BDD le nom de la nouvelle carte
        $.ajax(
            {
              url: app.apiUrl + '/lists/' + $listId + '/cards/add', // /lists/[i:id]/cards/add
              method: 'POST', // La méthode HTTP souhaité pour l'appel Ajax (GET ou POST)
              dataType: 'json', // Le type de données attendu en réponse (text, html, xml, json)
              data: { // (optionnel) Tableau contenant les données à envoyer avec la requête
                cardName: $inputElement
            }
            }
          ).done(function(response) { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec succès" et je récupère le code de réponse en paramètre
                console.log(response); // debug
                //Masquer formulaire
                app.hiddeAddCardModal();
                //Générer et afficher la nouvelle carte
                var $cardElement = app.generateCardElement(response.model.title, response.model.id);
                $cardElement.appendTo('.panel-block');
          }).fail(function() { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec erreur"
              alert('Réponse ajax incorrecte');
          });
        //Masquer le formulaire
        app.hiddeAddCardModal();
    },

    //Méthode permettant de masquer le titre d'une carte et d'afficher le formulaire pour le renseigné
    handleDblClickOnListTitle: function(event) {
        console.log('double click');
        //cacher l'élément lié à l'évenement (currentTarget)
        //target => élement sur lequel on a doublecliqué
        //currentTarget => élement sur lequel on a attaché l'event
        var $h2Element = $(event.currentTarget);
        $h2Element.addClass('is-hidden');
        //Afficher le formulaire de renommage d'une liste
        //Et préremplir l'input par le contenu actuel
        var $formElement = $h2Element.next(); //next() sélectionne l'élement suivant (après le h2 ciblé -> form)
        var $currentTitle = $h2Element.text();
        $formElement.find('input.input').val($currentTitle);
        $formElement.removeClass('is-hidden');
        //On ajoute un focus sur le champ de l'input (une fois qu ele formuaire est affiché sinon marche pô)
        $formElement.find('input.input').focus();
    },

    //Méthode permettant l'ajout d'un bouton d'ajout de liste dans le DOM
    addListAddingBtn: function() {
        //Créer chaque élément avec leurs classes
        //<div class="column">
        var $rootElement = $('<div>').addClass('column').attr('id', 'add-list-btn-column'); //je créé la div column
        //<button class="button is-success" id="addListButton">
        var $btnElement = $('<button>').addClass('button is-success').attr('id', 'addListButton'); //je crée le bouton (attr permet l'ajout d'attibut (id, nom) ou (src, lien))
        //j'ajoute le text dans le bouton
        $btnElement.html('&nbsp; Ajouter une liste');
        //<span class="icon is-small">
        var $spanElement = $('<span>').addClass('icon is-small'); //je crée le span
        //<i class="fas fa-plus"></i>
        var $iElement = $('<i>').addClass('fas fa-plus'); //je crée le span

        //Ensuite on ajoute les éléments les uns dans les autres
        //$rootElement.append($btnElement.append($spanElement.append($iElement))); 
        $iElement.appendTo($spanElement);
        $spanElement.prependTo($btnElement); //prependTo car je veux ajouter l'enfant span avant le texte
        $btnElement.appendTo($rootElement);

        //Juste avant on ajoute les EventListener sur les éléments crées
        $btnElement.on('click', app.displayAddListModal);

        //Et enfin j'ajoute au DOM
        $rootElement.appendTo($('#lists'));
    },

    //Méthode permettant de modifier le nom de la liste où dblclickée
    changeListName: function(event) {
        //bloque le rafraichissement de la page
        event.preventDefault();
        //récupération de la valeur de l'input
        var $formElement = $(event.currentTarget);
        var $inputElement = $formElement.find('.update-list-name').val().trim(); 
        //Récupération de l'id de la liste dblclickée
        var $listId = $(event.currentTarget).data('id');
        //Envoyer vers la BDD le nouveau nom de la liste
        $.ajax(
            {
              url: app.apiUrl + '/lists/' + $listId + '/update', // URL sur laquelle faire l'appel Ajax
              method: 'POST', // La méthode HTTP souhaité pour l'appel Ajax (GET ou POST)
              dataType: 'json', // Le type de données attendu en réponse (text, html, xml, json)
              data: { // (optionnel) Tableau contenant les données à envoyer avec la requête
                listName: $inputElement
                //second: 'seconde donnée envoyée'
                }
            }
          ).done(function(response) { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec succès" et je récupère le code de réponse en paramètre
                //Masquer formulaire et afficher le nouveau titre
                $formElement.addClass('is-hidden');
                var $h2Element = $formElement.prev();
                $h2Element.removeClass('is-hidden');
                if(response.code == 1) {
                    $h2Element.text(response.model.name)
                }
                else {
                    alert(response.error);
                };
          }).fail(function() { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec erreur"
              alert('Réponse ajax incorrecte');
          });
          
    },

    //Méthode permettant de charger toutes les listes avec requetes ajax
    loadLists: function() {
        //Appel ajax vers les fichiers json
        $.ajax(
            {
              url: app.apiUrl + '/lists', // URL sur laquelle faire l'appel Ajax
              method: 'GET', // La méthode HTTP souhaité pour l'appel Ajax (GET ou POST)
              dataType: 'json' // Le type de données attendu en réponse (text, html, xml, json)
            }
          ).done(function(response) { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec succès" et je récupère le code de réponse en paramètre
                //console.log(response); // debug
                //On parcours le tableau de listes
                var $currentListElement; //je déclare ma var avant la boucle
                $.each(response, function(currentIndex, currentList) {
                    //console.log(currentList);
                    //pour chaque liste on crée un nouvel élément "list"
                    $currentListElement = app.generateListElement(currentList.name, currentList.id);
                    //charger les élément Cards
                    app.loadListCards(currentList.id, $currentListElement);
                    //puis on l'ajoute dans le DOM
                    $currentListElement.insertBefore('#add-list-btn-column');
                    
                });
                
          }).fail(function() { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec erreur"
              alert('Réponse ajax incorrecte');
          });
    },

    //Méthode permettant de charger toutes les cartes d'une liste avec requetes ajax
    loadListCards: function(listId, $listElementToAdd) {
        //Appel ajax vers les fichiers json
        $.ajax(
            {
              url: app.apiUrl + '/lists/' + listId + '/cards', // URL sur laquelle faire l'appel Ajax
              method: 'GET', // La méthode HTTP souhaité pour l'appel Ajax (GET ou POST)
              dataType: 'json' // Le type de données attendu en réponse (text, html, xml, json)
            }
          ).done(function(response) { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec succès" et je récupère le code de réponse en paramètre
                //console.log(response); // debug
                var $cardsParent = $listElementToAdd.find('.panel-block');
                //On parcours le tableau de listes
                var $currentCardElement; //je déclare ma var avant la boucle
                $.each(response, function(currentIndex, currentCard) {
                    //console.log('id de la card: '+ currentCard.id);
                    //pour chaque liste on crée un nouvel élément "list"
                    $currentCardElement = app.generateCardElement(currentCard.title, currentCard.id);
                    //puis on l'ajoute dans le DOM
                    $cardsParent.append($currentCardElement) ;
                });
                //j'active jQuery UI Sortable sur tous les div parents des cards
                $cardsParent.sortable({
                    connectWith: ".panel-block"
                  });
                //on écoute l'event sortupdate sur la div parent des cars
                $cardsParent.on('sortupdate', app.handleSortableUpdate);
          }).fail(function() { // J'attache une fonction anonyme à l'évènement "Appel ajax fini avec erreur"
              alert('Réponse ajax incorrecte');
          });
    },

    //Méthode retournant un élément liste personnalisé
    generateListElement: function(listName, listId) {
        //console.log('generateListElement');
        //console.log(listName);
        //je clone un modèle de liste vide
        var $modelList = $('#model-list').contents().clone();
        //je sélectionne le h2 (titre de la liste)
        //j'applique au h2 ciblé (titre liste) le text provenant de l'input
        $modelList.find('h2').text(listName);
        //Je place l'id de la liste dans les data de div class panel
        var $listPanelElement = $($modelList[3]).data('id', listId);
        //je place l'id de la liste dans les data du form
        var $listElement = $($modelList[3]).find('form').data('id', listId);//avec data les données ne sont pas visible dans l'inspecteur
        //je place l'id de la liste dans les data de la div card-add
        var $listElement2 = $($modelList[3]).find('.card-add').data('id', listId);//avec data les données ne sont pas visible dans l'inspecteur
        //On attache l'event dblclick sur le h2 dans le liste
        //console.log('accès a list data-id :'+ $listPanelElement.data('id'));
        $modelList.find('h2').on('dblclick', app.handleDblClickOnListTitle);
        //On attache l'event submit sur le formulaire de chgt de nom de liste
        $listElement.on('submit', app.changeListName);
        //On attache l'event click sur l'ajout de card
        $listElement2.on('click', app.displayAddCardModal);
        //je retourne l'élément liste crée...
        return $modelList;
        
    },

    //Méthode retournant un élément liste personnalisé
    generateCardElement: function(cardTitle, cardId) {
        console.log('generateCardElement avec id: ' + cardId);
        //je clone un modèle de carte vide
        var $modelCard = $('#model-card').contents().clone();
        //je sélectionne la div et j'applique à la cible le text provenant de Ajax
        $modelCard.find('.card-title').text(cardTitle);
        //je place l'id de la carte dans les data 
        $modelCard.data('id', cardId);//avec data les données ne sont pas visible dans l'inspecteur
        console.log('accès a card data-id :'+ $modelCard.data('id'));
        //TODO : ajout l'event si nécessaire
        
        return $modelCard;
        
    },

    //Méthode pour mise à jour BDD lors du glisser/déposer des cartes
    handleSortableUpdate: function(event, ui) {
        //console.log(event);
        //console.log(ui);
        //On cible toutes les listes
        var $allLists = $('#lists').find('.panel');
        //on déclare nos variables avant la boucle
        var position, currentListId, $currentListElement, $allCards, $currentCardElement, currentCardId;
        //on parcours ces listes
        $allLists.each(function(currentIndex, currentVanillaListElement) {
            //pour chaque liste
            $currentListElement = $(currentVanillaListElement);
            //J'initialise le compteur position à 1
            position = 1;
            //récupérer l'id (data)
            currentListId = $currentListElement.data('id');
            console.log('listId=' + currentListId);
            //On cible toutes les cards enfant de la liste
            $allCards = $currentListElement.find('.box');
            //On parcours ces cards
            $allCards.each(function(currentIndex2, currentVanillaCardElement) {
                //Pour chaque card
                $currentCardElement = $(currentVanillaCardElement);
                //On récupère son id
                currentCardId = $currentCardElement.data('id');
                console.log('currentCardId=' + currentCardId);
                //On fait appel Ajax POST sur le endpoint /cards/[id]/update avec le listId et le compteur récupérés
                $.ajax({
                    url: app.apiUrl + '/cards/' + currentCardId + '/update',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                      listId: currentListId,
                      listOrder: position
                    }
                  }).done(function(response) {
                    console.log(response);
          
                    // si la modif a fonctionné
                    if (response.code == 1) {
          
                    }
                    else {
                      alert(response.error);
                    }
                  }).fail(function() {
                    alert('ajax failed');
                  });
          
                  // J'ajoute 1 au compteur/position
                  position++;
            });
        });
    }


    
};


$(app.init);