(function($){
    if (typeof window.pcgCoursesField === 'undefined') {
        return;
    }

    var settings = window.pcgCoursesField;
    var removeLabel = settings.labels && settings.labels.remove ? settings.labels.remove : '×';

    function initCoursesField($field){
        var $input = $field.find('.pcg-courses-input');
        var $hidden = $field.find('.pcg-courses-hidden');
        var $tagsContainer = $field.find('.pcg-courses-tags');
        var $suggestions = $field.find('.pcg-courses-suggestions');
        var selectedCourses = [];

        function readInitialSelection(){
            selectedCourses = [];
            $tagsContainer.find('[data-course-id]').each(function(){
                var $tag = $(this);
                var id = parseInt($tag.attr('data-course-id'), 10);
                var title = $tag.attr('data-course-title') || $tag.text();
                if(!isNaN(id)){
                    selectedCourses.push({ id: id, title: title });
                }
            });
            syncHiddenField();
        }

        function syncHiddenField(){
            var ids = selectedCourses.map(function(item){ return item.id; });
            $hidden.val(JSON.stringify(ids));
        }

        function renderTags(){
            $tagsContainer.empty();
            selectedCourses.forEach(function(course){
                var $tag = $('<span/>', {
                    'class': 'pcg-course-tag',
                    'data-course-id': course.id,
                    'data-course-title': course.title
                });

                $('<span/>', {
                    'class': 'pcg-course-tag__label',
                    text: course.title
                }).appendTo($tag);

                $('<button/>', {
                    type: 'button',
                    'class': 'pcg-course-tag__remove',
                    'aria-label': removeLabel,
                    html: '&times;'
                }).appendTo($tag);

                $tagsContainer.append($tag);
            });
        }

        function addCourse(course){
            if(!course || !course.id){
                return;
            }

            var exists = selectedCourses.some(function(item){
                return item.id === course.id;
            });

            if(exists){
                clearSuggestions();
                $input.val('');
                return;
            }

            selectedCourses.push(course);
            renderTags();
            syncHiddenField();
            clearSuggestions();
            $input.val('');
        }

        function removeCourse(id){
            selectedCourses = selectedCourses.filter(function(item){
                return item.id !== id;
            });
            renderTags();
            syncHiddenField();
        }

        function clearSuggestions(){
            $suggestions.empty().hide();
        }

        function showSuggestions(results){
            $suggestions.empty();
            if(!results || !results.length){
                $suggestions.hide();
                return;
            }

            var $list = $('<ul/>', { 'class': 'pcg-courses-suggestions__list' });

            results.forEach(function(item){
                var $option = $('<li/>', {
                    'class': 'pcg-courses-suggestions__item',
                    text: item.title,
                    'data-id': item.id
                });

                $option.on('mousedown', function(e){
                    e.preventDefault();
                    addCourse({ id: item.id, title: item.title });
                });

                $list.append($option);
            });

            $suggestions.append($list).show();
        }

        function searchCourses(query){
            if(!query || query.length < 2){
                clearSuggestions();
                return;
            }

            $.ajax({
                url: settings.ajaxUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: settings.action,
                    nonce: settings.nonce,
                    q: query
                }
            }).done(function(response){
                if(response && response.success){
                    showSuggestions(response.data);
                } else {
                    clearSuggestions();
                }
            }).fail(clearSuggestions);
        }

        $input.on('input', function(){
            var value = $(this).val();
            searchCourses(value);
        });

        $input.on('keydown', function(event){
            if(event.key === 'Enter'){
                event.preventDefault();
                var $firstSuggestion = $suggestions.find('.pcg-courses-suggestions__item').first();
                if($firstSuggestion.length){
                    addCourse({
                        id: parseInt($firstSuggestion.data('id'), 10),
                        title: $firstSuggestion.text()
                    });
                }
            } else if(event.key === 'Backspace' && !$(this).val().length && selectedCourses.length){
                removeCourse(selectedCourses[selectedCourses.length - 1].id);
            }
        });

        $tagsContainer.on('click', '.pcg-course-tag__remove', function(){
            var $tag = $(this).closest('[data-course-id]');
            var id = parseInt($tag.attr('data-course-id'), 10);
            if(!isNaN(id)){
                removeCourse(id);
            }
        });

        $(document).on('click', function(event){
            if(!$(event.target).closest('.pcg-courses-field').length){
                clearSuggestions();
            }
        });

        readInitialSelection();
    }

    $(document).ready(function(){
        $('.pcg-courses-field').each(function(){
            initCoursesField($(this));
        });
    });
})(jQuery);
