<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="main.xslt" />

<!--
//
//  Login template
//
//-->
<xsl:template match="login">
<xsl:call-template name="show_messages" />
<form action="{url}" method="post" autocomplete="off" onSubmit="javascript:hash_password(); return true;">
<label for="username">Gebruikersnaam:</label>
<input type="text" autocapitalize="off" autocorrect="off" id="username" name="username" value="{username}" class="form-control" />
<label for="password">Wachtwoord:</label>
<input type="password" id="password" name="password" class="form-control" />
<p>Sessie verbinden aan IP-adres (<xsl:value-of select="remote_addr" />): <input type="checkbox" name="bind_ip">
<xsl:if test="bind">
<xsl:attribute name="checked">checked</xsl:attribute>
</xsl:if>
</input></p>
<div class="btn-group">
<input type="submit" value="Inloggen" class="btn btn-default" />
</div>

<input type="hidden" id="use_cr_method" name="use_cr_method" value="no" />
</form>
<input type="hidden" id="challenge" value="{challenge}" />

<xsl:if test="@wachtwoord='yes'"><p>Als u uw wachtwoord vergeten bent, klik dan <a href="/wachtwoord">hier</a>.</p></xsl:if>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Inloggen</h1>
<xsl:apply-templates select="login" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
