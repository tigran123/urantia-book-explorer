(function ($) {
    $.fn.scrollSync = function () {
        var $this = $(this);
        $this.on('scroll', function (e) {
            var $sender = $(e.currentTarget);
            if ($sender.is(":hover") && $sender.hasClass('scrollable')) {
                var percentage = this.scrollTop / (this.scrollHeight - this.offsetHeight);
                $this.not($sender).each(function (i, other) {
                    other.scrollTop = percentage * (other.scrollHeight - other.offsetHeight);
                });
            }
        });
    };
})(jQuery);