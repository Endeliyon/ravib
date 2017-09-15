<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  request form template
//
//-->
<xsl:template match="request">
<p>Voer eerst uw gebruikersnaam en e-mailadres in.</p>
<form action="/{/output/page}" method="post">
<label for="username">Gebruikersnaam:</label>
<input type="text" id="username" name="username" class="form-control" />
<label for="email">E-mailadres:</label>
<input type="text" id="email" name="email" class="form-control" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Opsturen" class="btn btn-default" />
<a href="/" class="btn btn-default">Afbreken</a>
</div>
</form>
</xsl:template>

<!--
//
//  Link sent template
//
//-->
<xsl:template match="link_sent">
<p>Indien u een geldige combinatie van gebruikersnaam en e-mailadres heeft ingevoerd, dan ontvangt u per e-mail een link waarmee een nieuw wachtwoord ingesteld kan worden.</p>
<p>Dit browservenster <i>niet</i> sluiten!</p>
</xsl:template>

<!--
//
//  Reset form template
//
//-->
<xsl:template match="reset">
<p>Voer een nieuw wachtwoord in voor uw account:</p>
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post" onSubmit="javascript:hash_passwords(); return true;">
<input type="hidden" name="key" value="{key}" />
<input type="hidden" id="username" value="{username}" />
<input type="hidden" id="password_hashed" name="password_hashed" value="no" />
<label for="password">Wachtwoord:</label>
<input type="password" id="password" name="password" class="form-control" />
<label for="repeat">Herhaal wachtwoord:</label>
<input type="password" id="repeat" name="repeat" class="form-control" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Wachtwoord opslaan" class="btn btn-default" />
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Wachtwoord vergeten</h1>
<xsl:apply-templates select="request" />
<xsl:apply-templates select="link_sent" />
<xsl:apply-templates select="reset" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
