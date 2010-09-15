function quote(commentId, authorId, authorName)
{
   var tagOpenQuote    = "[quote]";
   var tagOpenCite     = "[cite]";
   var tagCloseQuote   = "[/quote]";
   var tagCloseCite    = "[/cite]";
   
   var selText = getSel();
   
   var commentField = document.getElementById("CommentBox");
   
   var commentText = "";

   commentText = dumpCodeTree(document.getElementById("CommentBody_" + commentId));
   
   if(selText != "")
      commentText = selText;
   
   if(commentField.value != "")
      commentField.value += "\r\n" + "\r\n";
      
   commentField.value += tagOpenQuote;
   commentField.value += tagOpenCite;      
   commentField.value += authorName;      
   commentField.value += tagCloseCite;      
   commentField.value += commentText;      
   commentField.value += tagCloseQuote;
   
   commentField.focus();
}

function getSel()
{
   if(window.getSelection)
   {
      var d = document.createElement("p");
      var s = window.getSelection();
   
      for(var i = 0; i < s.rangeCount; ++i)
      {
         var r = s.getRangeAt(i);
         if (!r.collapsed) 
         {
            d.appendChild(r.cloneContents());
         }
      }
      return dumpCodeTree(d);
   }
   else
      return "";
}

function dumpCodeTree(root) 
{
   var children     = root.childNodes;
   var outputText   = "";
   
   //BBCode should use square brackets:
   
   var tagOpenPrefix  = "[";
   var tagOpenSuffix  = "]";
   var tagClosePrefix = "[/";
   var tagCloseSuffix = "]";
      
   for (var i = 0; i < children.length; i++) 
   {
      if(children[i].nodeType == 1)
      {
         var tagName = children[i].tagName.toLowerCase();
         
         //If html tags need to be changed into something else,
         //here is the place to do it:
         
         if(tagName == "br")
         {
            outputText += "\r\n";
            tagName = "";
         }
         
         if(tagName == "em")
         {
            tagName = "i";
         }

         if(tagName == "strong")
         {
            tagName = "b";
         }
         
         if(tagName == "blockquote")
         {
            tagName = "quote";
         }
         
         if(tagName == "a")
         {
            outputText += tagOpenPrefix;
            outputText += "url";
            
            for (var j = 0; j < children[i].attributes.length; j++) 
            {
               attributeName  = children[i].attributes[j].name;
               attributeValue = children[i].attributes[j].value;
               
               if(attributeName == "href" && attributeValue != dumpCodeTree(children[i]))
                  outputText += "=" + attributeValue;
            }
            
            outputText += tagOpenSuffix;
            outputText += dumpCodeTree(children[i]);
            outputText += tagClosePrefix;
            outputText += "url";
            outputText += tagCloseSuffix;
            
            tagName = "";
         }
         
         if(tagName == "img")
         {
            var imageText = ""

            var src = "";
            var alt = "";
            
            imageText += tagOpenPrefix;
            imageText += tagName;
            
            for (var j = 0; j < children[i].attributes.length; j++) 
            {
               attributeName  = children[i].attributes[j].name;
               attributeValue = children[i].attributes[j].value;
               
               if(attributeName == "width")
                  attributeName = "w";
               else if(attributeName == "height")
                  attributeName = "h";
               else if(attributeName == "src")
               {
                  src = attributeValue;
                  attributeName = "";
               }
               else if(attributeName == "alt")
               {
                  alt = attributeValue;
                  attributeName = "";
               }
               else
               {
                  attributeName = "";
               }
               
               if(attributeName != "" && attributeValue != "null" && attributeValue != "")
               {
                  imageText += " ";
                  imageText += attributeName;
                  imageText += "=";
                  imageText += attributeValue;
               }
            }
            
            imageText += tagOpenSuffix;
            imageText += src;
            imageText += tagClosePrefix;
            imageText += tagName;
            imageText += tagCloseSuffix;
            
            if(alt != "")
               outputText += alt;
            else
               outputText += imageText
            
            tagName = "";
         }         
         
         if(tagName != "")
         {
            outputText += tagOpenPrefix; 
            outputText += tagName;

            for (var j = 0; j < children[i].attributes.length; j++) 
            {
               attributeName  = children[i].attributes[j].name;
               attributeValue = children[i].attributes[j].value;
               
               //If html attributes need to be changed into something else,
               //here is the place to do it: 
                             
               if(attributeName == "target"          ||
                  attributeName == "hideFocus"       ||
                  attributeName == "contentEditable" ||
                  attributeName == "disabled"        ||
                  attributeName == "tabIndex")
                  {attributeName = "";}
               
               if(attributeName != "" && attributeValue != "null" && attributeValue != "")
               {
                  outputText += " ";
                  outputText += attributeName;
                  outputText += "=";
                  outputText += attributeValue;
               }
            }

            outputText += tagOpenSuffix;
            outputText += dumpCodeTree(children[i]);
            outputText += tagClosePrefix;
            outputText += tagName;
            outputText += tagCloseSuffix;
         }
      }
      else if(children[i].nodeType == 3)
      {
         var nodeValue = children[i].nodeValue;
         
         //strip out extra newlines/returns
         nodeValue = nodeValue.replace(/\r\n/g," ");
         nodeValue = nodeValue.replace(/\n/g," ");
         nodeValue = nodeValue.replace(/\r/g," ");
         
         //strip whitespace
         nodeValue = nodeValue.replace(/^\s*|\s*$/g,'');
         
         outputText += nodeValue;
      }
   }
   
   return outputText;
}