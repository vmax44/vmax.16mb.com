(function ($) {
    $(document).ready(function () {
        var loader = {
            stack: [],
            maxThreads: 6,
            currentThreads: 0,

            getData: function (o, done, fail) {
                this.stack.push({ o: o, done: done, fail: fail });
                this.loop();
            },

            loop: function () {
                if (this.currentThreads < this.maxThreads) {
                    var req = this.stack.pop();
                    if (req != null) {
                        //var _this = this;
                        this.currentThreads++;
                        var a = $.ajax(req.o);
                        if (req.done != null) {
                            a.done(req.done);
                        }
                        if (req.fail != null) {
                            a.fail(req.fail);
                        }
                        a.complete((function (context) {
                            return function () {
                                context.currentThreads--;
                                context.loop();
                            }
                        })(this));
                    }
                }
            }
        }

        var dataModel = {
            firms: []
        }

        var viewModel = {
            firms: ko.observableArray(),
            selected: ko.
        };

        ko.dependentObservable(function () {
            viewModel.firms(dataModel.firms);
        }, this);

        ko.applyBindings(viewModel);
        
        loader.getData("/www.inc.com/get_full_list.php",
            function (data) {
                dataModel.firms = JSON.parse(data);
                viewModel.firms(dataModel.firms);
            },
            function () {
                alert("Error get full list");
            }
        );

    });
})(jQuery);