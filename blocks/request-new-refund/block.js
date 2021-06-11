/**
 * Block: PMPro Membership Request-new-refund
 *
 * Displays the Membership Request-new-refund and form.
 *
 */

 /**
  * Internal block libraries
  */
  const { __ } = wp.i18n;
  const {
     registerBlockType
 } = wp.blocks;
 
  /**
   * Register block
   */
  export default registerBlockType(
      'pmpro/request-new-refund',
      {
          title: __( 'Membership Request New Refund', 'paid-memberships-pro' ),
          description: __( 'Displays the member\'s Request new refund information and allows them to update the payment method.', 'paid-memberships-pro' ),
          category: 'pmpro',
          icon: {
             background: '#2997c8',
             foreground: '#ffffff',
             src: 'list-view',
         },
         keywords: [ __( 'pmpro', 'paid-memberships-pro' ) ],
         supports: {
         },
         attributes: {
         },
          edit() {
              return [
                  <div className="pmpro-block-element">
                    <span className="pmpro-block-title">{ __( 'Paid Memberships Pro', 'paid-memberships-pro' ) }</span>
                    <span className="pmpro-block-subtitle">{ __( 'Membership Request New Refund', 'paid-memberships-pro' ) }</span>
                  </div>
             ];
          },
          save() {
            return null;
          },
        }
  );
 