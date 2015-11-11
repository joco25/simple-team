angular
    .module 'simple.team.routes', []
    .config [
        '$stateProvider',
        ($stateProvider) ->
            $stateProvider
                .state 'projects',
                    url: '/projects'
                    template: require './layouts/projects.html'
                    controller: require './controllers/projects.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'projects.card',
                    url: '/card/?cardId'
                    template: require './layouts/card.html'
                    controller: require './controllers/card.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'tasklist',
                    url: '/tasklist'
                    template: require './layouts/tasklist.html'
                    controller: require './controllers/tasklist.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'tasklist.card',
                    url: '/card/?cardId'
                    template: require './layouts/card.html'
                    controller: require './controllers/card.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'dailySummary',
                    url: '/dailySummary'
                    template: require './layouts/dailySummary.html'
                    controller: require './controllers/dailySummary.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'timeline',
                    url: '/timeline'
                    template: require './layouts/timeline.html'
                    controller: require './controllers/timeline.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'settings',
                    url: '/settings'
                    template: require './layouts/settings.html'
                    controller: require './controllers/settings.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'settings.account',
                    url: '/account'
                    template: require './layouts/settings.account.html'
                    controller: require './controllers/settings.account.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'settings.teams',
                    url: '/teams'
                    template: require './layouts/settings.teams.html'
                    controller: require './controllers/settings.teams.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'conversations',
                    url: '/conversations'
                    template: require './layouts/conversations.html'
                    controller: require './controllers/conversations.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'conversations.list',
                    url: '/list?type'
                    template: require './layouts/conversations.list.html'
                    controller: require './controllers/conversations.list.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'conversations.create',
                    url: '/create'
                    template: require './layouts/conversations.create.html'
                    controller: require './controllers/conversations.create.ctrl.coffee'
                    controllerAs: 'ctrl'

                .state 'conversations.view',
                    url: '/view?topicId'
                    template: require './layouts/conversations.view.html'
                    controller: require './controllers/conversations.view.ctrl.coffee'
                    controllerAs: 'ctrl'
    ]
