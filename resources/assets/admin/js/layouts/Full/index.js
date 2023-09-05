import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import {
  Frame, TopBar, Navigation
} from '@shopify/polaris';

import { logout } from '../../actions/common';

class Full extends Component {
  constructor(props) {
    super(props);
    this.state = {
      userMenuOpen: false,
      showMobileNavigation: false
    };
    this.toggleState = this.toggleState.bind(this);
    this.handleNavigation = this.handleNavigation.bind(this);
    this.handleLogout = this.handleLogout.bind(this);
    this.handleUrl = this.handleUrl.bind(this);
  }

  toggleState(key) {
    return () => {
      this.setState(prevState => ({ [key]: !prevState[key] }));
    };
  }

  handleNavigation(url) {
    return () => {
      this.props.history.push(url);
    };
  }

  handleUrl(url) {
    return () => {
      window.open(url, '_self');
    };
  }

  handleLogout() {
    this.props.logout();
    this.props.history.replace('/admin/login');
  }

  renderUserMenu() {
    const { auth } = this.props;
    const {
      userMenuOpen
    } = this.state;
    return (
      <TopBar.UserMenu
        actions={[
          {
            items: [
              { content: 'Profile', icon: 'profile' }
            ]
          },
          {
            items: [
              {
                content: 'Change password',
                icon: 'profile',
                onAction: this.handleNavigation('/admin/change-password')
              }
            ]
          },
          {
            items: [
              { content: 'Logout', icon: 'logOut', onAction: this.handleLogout }
            ]
          }
        ]}
        name={auth && auth.user.firstname}
        detail={auth && auth.user.email}
        initials={auth && auth.user.firstname[0]}
        open={userMenuOpen}
        onToggle={this.toggleState('userMenuOpen')}
      />
    );
  }

  renderNavigation() {
    return (
      <Navigation
        location="/"
      >
        <Navigation.Section
          items={[
            {
              label: 'Goto Bet Polls',
              icon: 'arrowLeft',
              onClick: this.handleUrl('/')
            }
          ]}
        />
        <Navigation.Section
          separator
          title="General"
          items={[
            {
              label: 'Settings',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/generals/settings')
            },
            {
              label: 'Users',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/generals/users')
            }
          ]}
          action={{
            icon: 'conversation',
            accessibilityLabel: 'Contact support',
            onClick: this.toggleState('modalActive')
          }}
        />
        <Navigation.Section
          separator
          title="Blogs"
          items={[
            {
              label: 'Pages',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/blogs/posts?post_type=page')
            },
            {
              label: 'Posts',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/blogs/posts?post_type=post')
            },
            {
              label: 'Home',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/blogs/posts?post_type=home')
            },
            {
              label: 'Contact',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/blogs/posts?post_type=contact')
            }
          ]}
          action={{
            icon: 'conversation',
            accessibilityLabel: 'Contact support',
            onClick: this.toggleState('modalActive')
          }}
        />
        <Navigation.Section
          separator
          title="Appearances"
          items={[
            {
              label: 'Menu',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/appearances/menus')
            },
            {
              label: 'Home',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/appearances/home')
            }
          ]}
          action={{
            icon: 'conversation',
            accessibilityLabel: 'Contact support',
            onClick: this.toggleState('modalActive')
          }}
        />
        <Navigation.Section
          separator
          title="Sports"
          items={[
            {
              label: 'Categories',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/sports/categories')
            },
            {
              label: 'Countries',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/sports/countries')
            },
            {
              label: 'Leagues',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/sports/leagues')
            },
            {
              label: 'Teams',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/sports/teams')
            },
            {
              label: 'Players',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/sports/players')
            }
          ]}
          action={{
            icon: 'conversation',
            accessibilityLabel: 'Contact support',
            onClick: this.toggleState('modalActive')
          }}
        />
        <Navigation.Section
          separator
          title="Bets"
          items={[
            {
              label: 'Games',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/bets/games')
            },
            {
              label: 'Leader Board',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/bets/leaderboard')
            }
          ]}
          action={{
            icon: 'conversation',
            accessibilityLabel: 'Contact support',
            onClick: this.toggleState('modalActive')
          }}
        />
        <Navigation.Section
          separator
          title="Manual Polls"
          items={[
            {
              label: 'Categories',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/manual/categories')
            },
            {
              label: 'Subcategories',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/manual/subcategories')
            },
            {
              label: 'Countries',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/manual/countries')
            },
            {
              label: 'Candidate Types',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/manual/candidate-types')
            },
            {
              label: 'Candidates',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/manual/candidates')
            },
            {
              label: 'Polls',
              icon: 'chevronRight',
              onClick: this.handleNavigation('/admin/manual/polls')
            }
          ]}
          action={{
            icon: 'conversation',
            accessibilityLabel: 'Contact support',
            onClick: this.toggleState('modalActive')
          }}
        />
      </Navigation>
    );
  }

  render() {
    const {
      showMobileNavigation
    } = this.state;
    return (
      <Frame
        topBar={(
          <TopBar
            showNavigationToggle
            userMenu={this.renderUserMenu()}
            onNavigationToggle={this.toggleState('showMobileNavigation')}
          />
        )}
        navigation={this.renderNavigation()}
        showMobileNavigation={showMobileNavigation}
        onNavigationDismiss={this.toggleState('showMobileNavigation')}
      >
        {this.props.children}
      </Frame>
    );
  }
}

const mapStateToProps = state => ({
  auth: state.common.auth
});

const mapDispatchToProps = dispatch => ({
  logout: bindActionCreators(logout, dispatch)
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Full));
