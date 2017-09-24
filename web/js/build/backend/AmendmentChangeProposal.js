define(["require","exports"],function(t,i){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var e=function(){function t(t){this.$widget=t,this.initElements(),this.initStatusSetter(),this.initCommentForm(),this.initVotingBlock(),this.initExplanation(),t.submit(function(t){return t.preventDefault()})}return t.prototype.initElements=function(){this.$statusDetails=this.$widget.find(".statusDetails"),this.$visibilityInput=this.$widget.find("input[name=proposalVisible]"),this.$votingStatusInput=this.$widget.find("input[name=votingStatus]"),this.$votingBlockId=this.$widget.find("input[name=votingBlockId]"),this.context=this.$widget.data("context"),this.saveUrl=this.$widget.attr("action"),this.csrf=this.$widget.find("input[name=_csrf]").val()},t.prototype.reinitAfterReload=function(){this.initElements(),this.statusChanged(),this.commentsScrollBottom(),this.initExplanation(),this.$widget.find(".newBlock").addClass("hidden"),this.$widget.find(".selectlist").selectlist()},t.prototype.performCallWithReload=function(t){var i=this;t._csrf=this.csrf,$.post(this.saveUrl,t,function(t){if(i.$widget.addClass("showSaved").removeClass("isChanged"),window.setTimeout(function(){return i.$widget.removeClass("showSaved")},2e3),t.success){var e=$(t.html);i.$widget.children().remove(),i.$widget.append(e.children()),i.reinitAfterReload()}else t.error?alert(t.error):alert("An error ocurred")}).fail(function(){alert("Could not save")})},t.prototype.notifyProposer=function(){this.performCallWithReload({notifyProposer:"1"})},t.prototype.saveStatus=function(){var t=this.$widget.find(".statusForm input[type=radio]:checked").val(),i={setStatus:t,visible:this.$visibilityInput.prop("checked")?1:0,votingBlockId:this.$votingBlockId.val()};10==t&&(i.proposalComment=this.$widget.find("input[name=referredTo]").val()),22==t&&(i.proposalComment=this.$widget.find("input[name=obsoletedByAmendment]").val()),23==t&&(i.proposalComment=this.$widget.find("input[name=statusCustomStr]").val()),11==t&&(i.votingStatus=this.$votingStatusInput.filter(":checked").val()),"NEW"==i.votingBlockId&&(i.votingBlockTitle=this.$widget.find("input[name=newBlockTitle]").val()),this.$widget.find("input[name=setPublicExplanation]").prop("checked")&&(i.proposalExplanation=this.$widget.find("textarea[name=proposalExplanation]").val()),this.performCallWithReload(i)},t.prototype.statusChanged=function(){var t=this.$widget.find(".statusForm input[type=radio]:checked").val();this.$statusDetails.addClass("hidden"),this.$statusDetails.filter(".status_"+t).removeClass("hidden"),0==t?this.$widget.addClass("noStatus"):this.$widget.removeClass("noStatus")},t.prototype.initStatusSetter=function(){var t=this;this.$widget.on("change",".statusForm input[type=radio]",function(i,e){$(i.currentTarget).prop("checked")&&(t.statusChanged(),e&&!0===e.init||t.$widget.addClass("isChanged"))}),this.$widget.find(".statusForm input[type=radio]").trigger("change",{init:!0}),this.$widget.on("change keyup","input, textarea",function(){t.$widget.addClass("isChanged")}),this.$widget.on("changed.fu.selectlist","#obsoletedByAmendment",function(){t.$widget.addClass("isChanged")}),this.$widget.on("click",".saving button",this.saveStatus.bind(this)),this.$widget.on("click",".notifyProposer",this.notifyProposer.bind(this))},t.prototype.initVotingBlock=function(){var t=this;this.$widget.on("changed.fu.selectlist","#votingBlockId",function(){t.$widget.addClass("isChanged"),"NEW"==t.$votingBlockId.val()?t.$widget.find(".newBlock").removeClass("hidden"):t.$widget.find(".newBlock").addClass("hidden")}),this.$widget.find(".newBlock").addClass("hidden")},t.prototype.initExplanation=function(){var t=this;this.$widget.find("input[name=setPublicExplanation]").change(function(i){$(i.target).prop("checked")?t.$widget.find("section.publicExplanation").removeClass("hidden"):t.$widget.find("section.publicExplanation").addClass("hidden")}),this.$widget.find("input[name=setPublicExplanation]").prop("checked")?this.$widget.find("section.publicExplanation").removeClass("hidden"):this.$widget.find("section.publicExplanation").addClass("hidden")},t.prototype.commentsScrollBottom=function(){var t=this.$widget.find(".proposalCommentForm .commentList");t[0].scrollTop=t[0].scrollHeight},t.prototype.initCommentForm=function(){var t=this;this.$widget.on("click",".proposalCommentForm button",function(){var i=t.$widget.find(".proposalCommentForm"),e=!1,n=i.find(".commentList"),s=i.find("textarea").val();""==s||e||(e=!0,$.post(t.saveUrl,{writeComment:s,_csrf:t.csrf},function(t){if(t.success){var s=$('<li><div class="header"><div class="date"></div><div class="name"></div></div><div class="comment"></div></li>');s.find(".date").text(t.comment.dateFormatted),s.find(".name").text(t.comment.username),s.find(".comment").text(t.comment.text),n.append(s),i.find("textarea").val(""),n[0].scrollTop=n[0].scrollHeight}else alert("Could not save: "+JSON.stringify(t));e=!1}).fail(function(){alert("Could not save"),e=!1}))}),this.commentsScrollBottom()},t}();i.AmendmentChangeProposal=e});
//# sourceMappingURL=AmendmentChangeProposal.js.map
